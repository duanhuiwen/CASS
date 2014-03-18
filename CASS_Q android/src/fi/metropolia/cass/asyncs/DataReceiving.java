package fi.metropolia.cass.asyncs;

import java.io.IOException;
import java.io.StringReader;
import java.net.UnknownHostException;
import java.util.ArrayList;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpResponseException;
import org.apache.http.client.ResponseHandler;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.BasicResponseHandler;
import org.apache.http.impl.client.DefaultHttpClient;
import org.xmlpull.v1.XmlPullParser;
import org.xmlpull.v1.XmlPullParserFactory;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.preference.PreferenceManager;
import android.util.Log;
import fi.metropolia.cass.application.ApplicationContext;
import fi.metropolia.cass.controllers.MainController;
import fi.metropolia.cass.main.R;
import fi.metropolia.cass.models.Answer;
import fi.metropolia.cass.models.Question;
import fi.metropolia.cass.models.Survey;

/**
 * This class downloads the data from the server. It extends AsyncTask.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class DataReceiving extends AsyncTask<Void, String, Boolean> {

	// ** Debugging **
	private final String TAG = this.getClass().getSimpleName();
	private static final boolean D = ApplicationContext.Debug;

	// ** Member objects **
	private MainController mController;
	private Survey mSurvey;
	private ProgressDialog mDialog;
	private String mError = "";

	/**
	 * Constructor.
	 * 
	 * @param context
	 *            Context of calling class
	 * @param controller
	 *            Object of calling class
	 */
	public DataReceiving(Context context, MainController controller) {
		if (D) Log.d(TAG, "constructor");

		this.mController = controller;
		this.mDialog = new ProgressDialog(context);
	}

	@Override
	protected void onPreExecute() {
		super.onPreExecute();
		if (D) Log.d(TAG, "++ ON PREEXECUTE ++");

		// ** Initialize and start loading dialog **
		mDialog.setMessage(ApplicationContext.getContext().getResources().getString(R.string.load));
		mDialog.setCancelable(true);
		mDialog.show();
		mDialog.setContentView(R.layout.dialog_load);
	}

	@Override
	protected void onPostExecute(Boolean result) {
		super.onPostExecute(result);
		if (D) Log.d(TAG, "-- ON POSTEXECUTE --");

		// ** Stop loading dialog **
		if (mDialog.isShowing()) {
			mDialog.dismiss();
		}
		// ** Check for result and inform calling controller **
		if (result) {
			// ** Send success message and received survey **
			mController.handleMessage(MainController.MESSAGE_SUCCESS, MainController.RESULT_DATA_RECEIVING, mSurvey);
		} else {
			// ** Send failure message with exception description **
			mController.handleMessage(MainController.MESSAGE_FAILURE, MainController.RESULT_DATA_RECEIVING, mError);
		}
	}

	@Override
	protected Boolean doInBackground(Void... params) {
		if (D) Log.d(TAG, "++ DO IN BACKGROUND ++");

		try {
			// ** Parse xml data and save in survey **
			mSurvey = parseXML();
		}catch (NullPointerException e){
			mError = ApplicationContext.getContext().getResources().getString(R.string.damaged_xml);
			Log.e(TAG, e.toString());
			return false;
		}catch (UnknownHostException e){
			mError = ApplicationContext.getContext().getResources().getString(R.string.check_question_server);
			Log.e(TAG, e.toString());
			return false;
		}catch (HttpResponseException e){
			mError = ApplicationContext.getContext().getResources().getString(R.string.check_question_server);
			Log.e(TAG, e.toString());
			return false;
		} catch (Exception e) {
			mError = e.getMessage();
			Log.e(TAG, e.toString());
			return false;
		} 
		return true;
	}

	/**
	 * Parse xml string and save in survey object.
	 * 
	 * @return Survey object filled with xml data
	 * @throws Exception
	 */
	private Survey parseXML() throws Exception {
		if (D) Log.d(TAG, "parseXML");

		// ** Local objects **
		Survey currentSurvey = null;
		ArrayList<Question> questions = new ArrayList<Question>();
		Question currentQuestion = null;
		ArrayList<Answer> answers = null;
		Answer currentAnswer = null;
		boolean correctTag = false;
		boolean message = false;

		// ** Get question server and token**
		SharedPreferences settings = PreferenceManager.getDefaultSharedPreferences(ApplicationContext.getContext());	
		String url = settings.getString("question_server", "") + "?uid=" + settings.getString("token", "");

		// ** Initialize xml pull parser **
		XmlPullParserFactory factory = XmlPullParserFactory.newInstance();
		factory.setNamespaceAware(true);
		XmlPullParser xpp = factory.newPullParser();

		// ** Set downloaded xml data string as input for parser **
		xpp.setInput(new StringReader(getXMLData(url)));

		// ** Start parsing process **
		int eventType = xpp.getEventType();
		// ** Parse until end of document is reached **
		while (eventType != XmlPullParser.END_DOCUMENT) {
			// ** Start of document **
			if (eventType == XmlPullParser.START_DOCUMENT) {
				// ** Check for any xml start tag **
			} else if (eventType == XmlPullParser.START_TAG) {
				// ** Check if start tag is survey and save data to survey **
				if (xpp.getName().equalsIgnoreCase("survey")) {
					currentSurvey = new Survey(Long.parseLong(xpp.getAttributeValue(null, "surveyId")));
					currentSurvey.setUserName(xpp.getAttributeValue(null, "username"));
					currentSurvey.setUserID(Long.parseLong(xpp.getAttributeValue(null, "uid")));
					currentSurvey.setSurveyCount(Integer.parseInt(xpp.getAttributeValue(null, "surveyCount")));
					
					if(xpp.getAttributeValue(null, "surveyTotal") != null && !xpp.getAttributeValue(null, "surveyTotal").equals("NaN")){
						currentSurvey.setSurveyTotal(Integer.parseInt(xpp.getAttributeValue(null, "surveyTotal")));
					}
					// ** Check if start tag is item and save data to question item **
				} else if (xpp.getName().equalsIgnoreCase("item")) {
					// ** Mark as true to get text between tags further below **
					correctTag = true;
					// ** Fill question with relevant data **
					currentQuestion = new Question(Long.parseLong(xpp.getAttributeValue(null, "q_id")));
					// ** Check for category and set all questions visible with category 0 **
					currentQuestion.setCategory(Integer.parseInt(xpp.getAttributeValue(null, "category")));
					int category = Integer.parseInt(xpp.getAttributeValue(null, "category"));
					if (category == 0) {
						currentQuestion.setVisible(true);
					}
					// ** Check for type and save relevant data depending on type of question **
					currentQuestion.setType(Integer.parseInt(xpp.getAttributeValue(null, "type")));
					int type = Integer.parseInt(xpp.getAttributeValue(null, "type"));
					if (type == 2 || type == 9) {
						currentQuestion.setMin(Integer.parseInt(xpp.getAttributeValue(null, "min")));
						currentQuestion.setMax(Integer.parseInt(xpp.getAttributeValue(null, "max")));
					}
					if (type == 9) {
						currentQuestion.setMinLabel(xpp.getAttributeValue(null, "minlabel"));
						currentQuestion.setMaxLabel(xpp.getAttributeValue(null, "maxlabel"));
					}
					// ** Set reference survey id **
					if (currentSurvey != null) {
						currentQuestion.setRefSID(currentSurvey.getSID());
					}

					// ** Make new array list for answers **
					answers = new ArrayList<Answer>();
					// ** Check if start tag is option and save data to answer object **
				} else if (xpp.getName().equalsIgnoreCase("option")) {
					currentAnswer = new Answer(Long.parseLong(xpp.getAttributeValue(null, "o_id")));
					currentAnswer.setContent(xpp.getAttributeValue(null, "value"));
					if (currentQuestion.getType() == 5) {
						currentAnswer.setCategory(Integer.parseInt(xpp.getAttributeValue(null, "category")));
					}
					if (currentQuestion != null) {
						currentAnswer.setRefQID(currentQuestion.getQID());
					}
					// ** Add answer to answer list **
					answers.add(currentAnswer);

					// ** Check if start tag is message.
					// In that case the xml contains a server-sided message,
					// which gets parsed further below **
				} else if (xpp.getName().equalsIgnoreCase("message")) {
					message = true;
				}

				// ** Check for any xml end tag **
			} else if (eventType == XmlPullParser.END_TAG) {
				// ** Check if end tag is survey and set question list to survey **
				if (xpp.getName().equalsIgnoreCase("survey")) {
					if (currentSurvey != null) {
						currentSurvey.setQuestions(questions);
					}

					// ** Check if end tag is item and add current question to question list **
				} else if (xpp.getName().equalsIgnoreCase("item")) {
					if (currentQuestion != null) {
						currentQuestion.setAnswers(answers);
						questions.add(currentQuestion);
					}
				}

				// ** Check for text between start and end tag **
			} else if (eventType == XmlPullParser.TEXT) {
				// ** Remove all spaces and \n in text **
				String str = xpp.getText().replaceAll(" ", "").replaceAll("\n", "");
				// ** Replace character codes with characters and set content to question **
				if (correctTag && str.length() > 0) {
					if (currentQuestion != null) {
						currentQuestion.setContent(xpp.getText().replaceAll("\n", "").replaceAll("%3F", "?"));
					}
					// ** Check if message and throw exception **
				} else if (message) {
					String err = "";
					if (str.length() < 1) {
						err = "Unknown error!";
					} else {
						err = xpp.getText().replaceAll("\n", "");
					}
					throw new Exception(err);
				}

				// ** End of document **
			} else if (eventType == XmlPullParser.END_DOCUMENT) {
				// ! Implement action here if needed !
			}
			eventType = xpp.next();
		}
		return currentSurvey;
	}

	/**
	 * Get the xml data from server.
	 * 
	 * @param url
	 *            Url of server
	 * @return String with xml data
	 * @throws ClientProtocolException
	 * @throws IOException
	 */
	private String getXMLData(String url) throws ClientProtocolException, IOException {
		if (D) Log.d(TAG, "getXMLData()");

		DefaultHttpClient client = new DefaultHttpClient();
		HttpGet getMethod = new HttpGet(url);
		ResponseHandler<String> responseHandler = new BasicResponseHandler();

		return client.execute(getMethod, responseHandler);
	}
}

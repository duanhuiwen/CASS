package fi.metropolia.cass.asyncs;

import java.io.BufferedInputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.UnknownHostException;
import java.sql.Timestamp;
import java.util.ArrayList;
import java.util.Calendar;

import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpResponseException;
import org.apache.http.client.ResponseHandler;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.FileEntity;
import org.apache.http.impl.client.BasicResponseHandler;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.protocol.HTTP;
import org.xmlpull.v1.XmlSerializer;

import android.app.Dialog;
import android.content.Context;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.preference.PreferenceManager;
import android.util.Log;
import android.util.Xml;
import android.view.Window;
import android.view.WindowManager;
import android.widget.ProgressBar;
import android.widget.TextView;
import fi.metropolia.cass.application.ApplicationContext;
import fi.metropolia.cass.controllers.MainController;
import fi.metropolia.cass.main.R;
import fi.metropolia.cass.models.Question;
import fi.metropolia.cass.models.Survey;

/**
 * This class sends the data to the server. It extends AsyncTask.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class DataSending extends AsyncTask<Void, String, Boolean> {

	// ** Debugging **
	private final String TAG = this.getClass().getSimpleName();
	private static final boolean D = ApplicationContext.Debug;

	// ** Buffer size for byte reading and writing **
	private static final int BUFFER_SIZE = 2 * 1024;

	// ** Member objects **
	private MainController mController = null;
	private Survey mSurvey = null;
	private File mXmlFile = null;
	private Dialog mDialog = null;
	private String mError = "";
	private String mMediaServer; 
	private String mMediaDirectory;
	private String mXmlDirectory;
	private ProgressBar mProgressBar = null;
	private TextView mTitle = null;
	private TextView mFilesCount = null;
	private int mFilesTotal = 0;
	private TextView mProgressPercent = null;

	/**
	 * Constructor.
	 * 
	 * @param context
	 *            Context of calling class
	 * @param controller
	 *            Calling controller or controller object of calling class
	 * @param survey
	 *            Survey to be sent
	 */
	public DataSending(Context context, MainController controller, Survey survey) {
		if (D) Log.d(TAG, "constructor");

		// ** Initialize objects **
		this.mController = controller;
		this.mSurvey = survey;
		this.mDialog = new Dialog(context, R.style.themeDialogCustom);

		// ** Make directories **
		mMediaDirectory = controller.makeDir("CASS/CASS Media");
		mXmlDirectory = controller.makeDir("CASS/xml");
		// ** Get media server url from preferences **
		SharedPreferences settings = PreferenceManager.getDefaultSharedPreferences(ApplicationContext.getContext());
		mMediaServer = settings.getString("media_server", "");
	}

	@Override
	protected void onPreExecute() {
		super.onPreExecute();
		if (D) Log.d(TAG, "++ ON PREEXECUTE ++");

		// ** Initialize data sending dialog including progress bar **
		mDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
		mDialog.setContentView(R.layout.dialog_progress);
		mProgressBar = (ProgressBar) mDialog.findViewById(R.id.progress_bar);
		mTitle = (TextView) mDialog.findViewById(R.id.title);
		mTitle.setText("");
		mFilesCount = (TextView) mDialog.findViewById(R.id.files);
		mFilesCount.setText("0/1");
		mProgressPercent = (TextView) mDialog.findViewById(R.id.progress_text);
		mProgressPercent.setText("0 %");
		Window window = mDialog.getWindow();
		window.setFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCH_MODAL, WindowManager.LayoutParams.FLAG_NOT_TOUCH_MODAL);
		window.clearFlags(WindowManager.LayoutParams.FLAG_DIM_BEHIND);
		mDialog.show();
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
			// ** Send success message **
			mController.handleMessage(MainController.MESSAGE_SUCCESS, MainController.RESULT_DATA_SENDING);
		} else {
			// ** Send failure message with exception description **
			mController.handleMessage(MainController.MESSAGE_FAILURE, MainController.RESULT_DATA_SENDING, mError);
		}
	}

	@Override
	protected void onProgressUpdate(String... values) {
		if (D) Log.d(TAG, "++ ON PROGRESSUPDATE ++");

		// ** Parse for type of update and update progress dialog,
		// P = Progress, T = Text, C = Count of files **
		if (values[0].startsWith("P")) {
			String progress = values[0].substring(1);
			int i = Integer.parseInt(progress);
			mProgressBar.setProgress(i);
			mProgressPercent.setText(i + " %");
		} else if (values[0].startsWith("T")) {
			String title = values[0].substring(1);
			mTitle.setText(title);
		} else if (values[0].startsWith("C")) {
			String count = values[0].substring(1);
			mFilesCount.setText(count + "/" + mFilesTotal);
		}
	}

	@Override
	protected Boolean doInBackground(Void... params) {
		if (D) Log.d(TAG, "++ DO IN BACKGROUND ++");

		try {
			publishProgress("TCreating XML file...");
			// ** Create xml file **
			createXMLFile(mSurvey);
			publishProgress("TSending answers...");
			// ** Send xml file to server **
			sendXMLFile();
			publishProgress("TUploading...");
			// ** Send media files to server **
			sendMediaFiles(mSurvey);
		} catch (NumberFormatException e){
			mError = e.toString();
			Log.e(TAG, e.toString());
			return false;
		}catch (UnknownHostException e){
			mError = ApplicationContext.getContext().getResources().getString(R.string.check_answer_servers);
			Log.e(TAG, e.toString());
			return false;
		}catch (HttpResponseException e){
			mError = ApplicationContext.getContext().getResources().getString(R.string.check_answer_servers);
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
	 * Create xml file with data from survey.
	 * 
	 * @param survey
	 *            Survey containing the data for xml file.
	 * @throws IOException
	 */
	private void createXMLFile(Survey survey) throws IOException {
		if (D) Log.d(TAG, "createXMLFile()");

		if (survey != null) {
			// ** Create new xml file **
			mXmlFile = new File(mXmlDirectory, "answers.xml");
			mXmlFile.createNewFile();

			// ** Create new file output stream and initialize with xml file **
			FileOutputStream fileOut = new FileOutputStream(mXmlFile);

			// ** Initialize xml serializer **
			XmlSerializer serializer = Xml.newSerializer();

			// ** Get date and time **
			Timestamp timestamp = new Timestamp(Calendar.getInstance().getTime().getTime());

			// ** Write document tag **
			serializer.setOutput(fileOut, "ISO-8859-1");
			serializer.startDocument(null, true);
			serializer.setFeature("http://xmlpull.org/v1/doc/features.html#indent-output", true);
			// ** Write new start tag "surveyAnswer" **
			serializer.startTag(null, "surveyAnswer");
			// ** Write new start tag "timestamp" and fill with data **
			serializer.startTag(null, "timestamp");
			serializer.attribute(null, "stamp", timestamp.toString());
			serializer.endTag(null, "timestamp");
			// ** Write new start tag "surveyId" and fill with data **
			serializer.startTag(null, "surveyId");
			serializer.attribute(null, "id", "" + survey.getSID());
			serializer.endTag(null, "surveyId");
			// ** Write new start tag "userName" and fill with data **
			serializer.startTag(null, "userName");
			serializer.attribute(null, "name", "" + survey.getUserID());
			serializer.endTag(null, "userName");
			// ** Write new item start tag for every question and fill with data **
			for (int i = 0; i < survey.getQuestions().size(); i++) {
				// ** Check if question is answered and visible and not empty.
				// Invisible or empty question don't get sent **
				if (survey.getQuestions().get(i).isAnswered() && survey.getQuestions().get(i).isVisible() && survey.getQuestions().get(i).getQID() != -1) {
					// item
					serializer.startTag(null, "item");
					serializer.attribute(null, "q_id", "" + survey.getQuestions().get(i).getQID());
					serializer.attribute(null, "type", "" + survey.getQuestions().get(i).getType());
					if (survey.getQuestions().get(i).getSelectedAID().startsWith("-")) {
						serializer.attribute(null, "answer", survey.getQuestions().get(i).getSelectedAnswer().getContent());
					} else {
						serializer.attribute(null, "answer", survey.getQuestions().get(i).getSelectedAID());
					}
					// ** Write item end tag **
					serializer.endTag(null, "item");
				}
			}

			// ** Write end tag "surveyAnswer"
			serializer.endTag(null, "surveyAnswer");
			serializer.endDocument();
			serializer.flush();
			// ** Close output stream **
			fileOut.close();
		}
	}

	/**
	 * Send xml file to server.
	 * 
	 * @throws ClientProtocolException
	 * @throws IOException
	 */
	private void sendXMLFile() throws ClientProtocolException, IOException {
		if (D) Log.d(TAG, "sendXMLFile()");

		// ** Get answer server from preferences **
		SharedPreferences settings = PreferenceManager.getDefaultSharedPreferences(ApplicationContext.getContext());
		String url = settings.getString("answer_server", "");
		// ** Initialize new http client **
		DefaultHttpClient client = new DefaultHttpClient();
		HttpPost postMethod = new HttpPost(url);
		// ** New response handler **
		ResponseHandler<String> responseHandler = new BasicResponseHandler();
		// ** Set entity of post with file **
		FileEntity fileEntity = new FileEntity(mXmlFile, HTTP.ISO_8859_1);
		fileEntity.setContentType("text/xml");
		postMethod.setEntity(fileEntity);
		// ** Send data to server **
		String response = client.execute(postMethod, responseHandler);
		if (D) Log.d(TAG, "-> Response: " + response);

	}

	/**
	 * Send media files to media server.
	 * 
	 * @param survey
	 *            Survey containing media paths.
	 * @throws IOException
	 */
	private void sendMediaFiles(Survey survey) throws IOException {
		if (D) Log.d(TAG, "sendMediaFiles()");

		// ** Copy questions in new array list **
		ArrayList<Question> questions = survey.getQuestions();

		int currentFile = 0;
		if (questions != null) {
			// ** Get total numbers of files **
			for (int i = 0; i < questions.size(); i++) {
				if (questions.get(i).getType() == MainController.VIDEO || questions.get(i).getType() == MainController.PHOTO || questions.get(i).getType() == MainController.AUDIO) {
					mFilesTotal++;
				}
			}

			for (int i = 0; i < questions.size(); i++) {
				// ** Check if question is answered and visible and not empty **
				if (questions.get(i).isAnswered() && questions.get(i).isVisible() && questions.get(i).getQID() != -1) {
					// ** Check if file is video or photo **
					if (questions.get(i).getType() == MainController.VIDEO || questions.get(i).getType() == MainController.PHOTO) {
							// ** Check if answer is accessible **
							if (questions.get(i).getSelectedAnswer() != null) {
							// ** If file is video or photo, check where photo is located and
							// write file in buffered in stream **
							BufferedInputStream bufferedInStream = null;
							File file = new File(mMediaDirectory, questions.get(i).getSelectedAnswer().getContent());
							if (file.exists()) {
								InputStream inStream = new FileInputStream(file);
								bufferedInStream = new BufferedInputStream(inStream, BUFFER_SIZE);
							} else {
								InputStream inStream = ApplicationContext.getContext().getContentResolver().openInputStream(questions.get(i).getSelectedAnswer().getMediaUri());
								bufferedInStream = new BufferedInputStream(inStream, BUFFER_SIZE);
							}
							// ** Update count of current file in sending dialog **
							publishProgress("C" + String.valueOf(++currentFile));
							// ** Send file to server **
							sendFile(questions.get(i).getSelectedAnswer().getContent(), String.valueOf(questions.get(i).getQID()), bufferedInStream);

						} else if (questions.get(i).getType() == MainController.AUDIO) {
							// ** If file is audio, write file in buffered input stream **
							File file = new File(mMediaDirectory, questions.get(i).getSelectedAnswer().getContent());
							InputStream inStream = new FileInputStream(file);
							BufferedInputStream bufferedInStream = new BufferedInputStream(inStream, BUFFER_SIZE);
							// ** Update count of current file in sending dialog **
							publishProgress("C" + String.valueOf(++currentFile));
							// ** Send file to server **
							sendFile(questions.get(i).getSelectedAnswer().getContent(), String.valueOf(questions.get(i).getQID()), bufferedInStream);
						}
					}
				}
			}
		}
	}

	/**
	 * Sent one file to server.
	 * 
	 * @param fname
	 *            Name of file
	 * @param QID
	 *            ID of question
	 * @param inStream
	 *            Buffred input stream with file data
	 * @throws IOException
	 */
	private void sendFile(String fname, String QID, BufferedInputStream inStream) throws IOException {
		if (D) Log.d(TAG, "sendFile()");
		
		// ** Set url and open new http url connection **
		URL url = new URL(mMediaServer);
		HttpURLConnection connection = (HttpURLConnection) url.openConnection();
		// ** Allow in- and outputs
		connection.setDoInput(true);
		connection.setDoOutput(true);
		connection.setUseCaches(false);
		// ** Set preferences for connection **
		connection.setRequestMethod("POST");
		connection.setRequestProperty("User-Agent", "Profile/MIDP-2.0 Configuration/CLDC-1.0");
		connection.setRequestProperty("Content-Type", "application/octet-stream");
		connection.setRequestProperty("Connection", "close");
		if (D) Log.d(TAG, "-> connection opened");
		// ** Get output stream of connection **
		OutputStream outStream = connection.getOutputStream();
		// ** Setup first 128 characters of string with filename
		// and question id, fill rest with spaces **
		String fileName = makeLength(fname, fname.length());
		String comma = ";";
		String id = makeLength(QID, 128 - fname.length() - comma.length());
		// ** Write front string to output stream in bytes **
		outStream.write(fileName.getBytes());
		outStream.write(comma.getBytes());
		outStream.write(id.getBytes());
		// ** Initialize byte array for data transfer **
		byte[] dataBuffer = new byte[BUFFER_SIZE];
		// ** Get total bytes in the file **
		long totalBytes = inStream.available();
		if (D) Log.d(TAG, "-> name: " + fname + " Total bytes: " + totalBytes);
		// ** Variables for writing progress **
		long bytesRead = 0;
		int n = 0;
		// ** Update file name for dialog and set progress to 0 **
		publishProgress("TUploading... " + fname);
		publishProgress("P" + String.valueOf(0));
		// ** Write file data to output stream **
		while ((n = inStream.read(dataBuffer, 0, BUFFER_SIZE)) != -1) {
			outStream.write(dataBuffer, 0, n);
			bytesRead += n;
			// ** Update progress of writing in progress dialog **
			publishProgress("P" + String.valueOf((int) (bytesRead * 100 / totalBytes)));
		}
		if (D) Log.d(TAG, "-> bytes read: " + bytesRead);
		
		// ** Get response code and message from server **
		// if (D) Log.d(TAG, String.valueOf(connection.getResponseCode()));
		// if (D) Log.d(TAG, connection.getResponseMessage());

		// ** Close streams **
		outStream.flush();
		outStream.close();
		inStream.close();
	}

	/**
	 * Fill end of string with spaces.
	 * 
	 * @param string
	 *            String to be filled
	 * @param length
	 *            Desired length string should be
	 * @return
	 */
	private String makeLength(String string, int length) {
		int stringLength = string.length();
		for (int i = 0; i < length - stringLength; i++) {
			string += " ";
		}
		return string;
	}

}

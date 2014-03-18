package fi.metropolia.cass.controllers;

import java.io.File;
import java.io.IOException;
import java.sql.Timestamp;
import java.text.SimpleDateFormat;
import java.util.Calendar;

import android.content.Context;
import android.content.SharedPreferences;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.media.ExifInterface;
import android.net.Uri;
import android.os.Environment;
import android.os.Handler;
import android.os.HandlerThread;
import android.preference.PreferenceManager;
import android.provider.MediaStore.MediaColumns;
import android.util.FloatMath;
import android.util.Log;
import fi.metropolia.cass.application.ApplicationContext;
import fi.metropolia.cass.asyncs.DataReceiving;
import fi.metropolia.cass.asyncs.DataSending;
import fi.metropolia.cass.asyncs.FileMoving;
import fi.metropolia.cass.daos.SQLiteManager;
import fi.metropolia.cass.main.R;
import fi.metropolia.cass.models.DataModel;
import fi.metropolia.cass.models.Question;
import fi.metropolia.cass.models.Survey;

/**
 * This class controls the whole program run. It provides methods for data storing, server communication, data transfer, media operations and works
 * for the activities and fragments.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class MainController extends Controller {

	// ** Debugging **
	private final String TAG = this.getClass().getSimpleName();
	private static final boolean D = ApplicationContext.Debug;

	// ** Message types send to activities and fragments **
	public static final int MESSAGE_TOAST = 101;
	public static final int MESSAGE_DIALOG = 201;
	public static final int MESSAGE_TOKEN_DIALOG = 301;
	public static final int MESSAGE_STORAGE_DIALOG = 401;
	public static final int MESSAGE_UPDATE_LIST = 501;

	// ** Message types sent from classes **
	public static final int MESSAGE_SUCCESS = 100;
	public static final int MESSAGE_FAILURE = 200;
	public static final int MESSAGE_SAVE_DATA = 300;

	// ** Result types of operations **
	public static final int RESULT_FILE_STORING = 400;
	public static final int RESULT_DATA_RECEIVING = 500;
	public static final int RESULT_DATA_SENDING = 600;
	public static final int RESULT_FILE_MOVING = 700;

	// ** Keys for creating fragments **
	public static final int OPEN_TEXT = 1;
	public static final int OPEN_NUMBER = 2;
	public static final int AUDIO = 3;
	public static final int SINGLE_CHOICE = 4;
	public static final int SUPER = 5;
	public static final int COMMENT = 6;
	public static final int PHOTO = 7;
	public static final int VIDEO = 8;
	public static final int SLIDER = 9;
	public static final int MULTIPLE_CHOICE = 10;

	// ** Length of the identification number(token) **
	public static final int TOKEN_LENGTH = 12;

	// ** Member objects **
	private final Handler mHandler;
	private DataModel mModel = null;
	private SQLiteManager mDatasource = null;
	private Context mContext = null;
	private SharedPreferences mSettings = null;
	private HandlerThread mWorkerThread = null;
	private Handler mWorkerHandler = null;

	/**
	 * Constructor.
	 * 
	 * @param context
	 *            Context of calling class.
	 * @param model
	 *            Data model.
	 * @param handler
	 *            Handler of calling class.
	 */
	public MainController(Context context, DataModel model, Handler handler) {
		if (D) Log.d(TAG, "contructor: (Context, DataModel, Handler)");

		// ** Initialize member objects **
		this.mContext = context;
		this.mModel = model;
		this.mHandler = handler;
		this.mDatasource = SQLiteManager.getInstance();
		this.mSettings = PreferenceManager.getDefaultSharedPreferences(ApplicationContext.getContext());
		// ** Initialize worker Thread and Handler **
		mWorkerThread = new HandlerThread("Worker Thread");
		mWorkerThread.start();
		mWorkerHandler = new Handler(mWorkerThread.getLooper());
	}

	/**
	 * Constructor.
	 * 
	 * @param model
	 *            Data model.
	 * @param handler
	 *            Handler of calling class.
	 */
	public MainController(DataModel model, Handler handler) {
		if (D) Log.d(TAG, "contructor: (DataModel, Handler)");

		// ** Initialize member objects **
		this.mModel = model;
		this.mHandler = handler;
		this.mDatasource = SQLiteManager.getInstance();
		this.mSettings = PreferenceManager.getDefaultSharedPreferences(ApplicationContext.getContext());
		// ** Initialize worker Thread and Handler **
		mWorkerThread = new HandlerThread("Worker Thread");
		mWorkerThread.start();
		mWorkerHandler = new Handler(mWorkerThread.getLooper());
	}

	/**
	 * Constructor.
	 * 
	 * @param context
	 *            Context of calling class.
	 * @param model
	 *            Data model.
	 * @param handler
	 *            Handler of calling class.
	 */
	public MainController(Context context, DataModel model) {
		if (D) Log.d(TAG, "contructor: (Context, DataModel)");

		// ** Initialize member objects **
		this.mContext = context;
		this.mModel = model;
		this.mHandler = null;
		this.mDatasource = SQLiteManager.getInstance();
		this.mSettings = PreferenceManager.getDefaultSharedPreferences(ApplicationContext.getContext());
		// ** Initialize worker Thread and Handler **
		mWorkerThread = new HandlerThread("Worker Thread");
		mWorkerThread.start();
		mWorkerHandler = new Handler(mWorkerThread.getLooper());
	}

	/**
	 * Constructor.
	 * 
	 * @param model
	 *            Data model.
	 */
	public MainController(DataModel model) {
		if (D) Log.d(TAG, "contructor: (DataModel)");

		// ** Initialize member objects **
		this.mModel = model;
		this.mHandler = null;
		this.mDatasource = SQLiteManager.getInstance();
		this.mSettings = PreferenceManager.getDefaultSharedPreferences(ApplicationContext.getContext());
		// ** Initialize worker Thread and Handler **
		mWorkerThread = new HandlerThread("Worker Thread");
		mWorkerThread.start();
		mWorkerHandler = new Handler(mWorkerThread.getLooper());
	}

	// **************************************************
	// *** Super class methods
	// **************************************************

	/** Quit worker thread. */
	@Override
	public void dispose() {
		super.dispose();
		if (D) Log.d(TAG, "dispose()");

		// ** Quit worker thread **
		mWorkerThread.getLooper().quit();
	}

	/** Handle messages from calling classes. */
	@Override
	public boolean handleMessage(int what, int result, Object data, String message, boolean option) {
		if (D) Log.d(TAG, "handleMessage()");

		switch (what) {
		case MESSAGE_SUCCESS:
			switch (result) {
			case RESULT_DATA_RECEIVING:
				// ** Save survey if data receiving was successful **
				saveReceivedData((Survey) data);
				return true;

			case RESULT_DATA_SENDING:
				// ** Show toast on calling interface class if data sending was
				// successful **
				if(mHandler != null){
					mHandler.obtainMessage(MESSAGE_TOAST, ApplicationContext.getContext().getResources().getString(R.string.data_sent)).sendToTarget();
				}
				deleteSurvey();
				getDataFromServer();
				return true;

			case RESULT_FILE_MOVING:
				// ** Delete old media files if they have been copied successfully **
				deleteFiles();
				return true;
			}

		case MESSAGE_FAILURE:
			switch (result) {
			case RESULT_DATA_RECEIVING:
				// ** Show alert dialog if data receiving failed **
				if(mHandler != null){
					mHandler.obtainMessage(MESSAGE_DIALOG, ApplicationContext.getContext().getResources().getString(R.string.receive_failed) + " " + message).sendToTarget();
				}
				return true;

			case RESULT_DATA_SENDING:
				// ** Show alert dialog if data sending failed **
				if(mHandler != null){	
					mHandler.obtainMessage(MESSAGE_DIALOG, ApplicationContext.getContext().getResources().getString(R.string.send_failed) + " " + message).sendToTarget();
				}
				return true;
			}
		}
		return false;
	}

	// **************************************************
	// *** Initialization methods
	// **************************************************

	/** Initialize application. */
	public void init() {
		if (D) Log.d(TAG, "init()");

		// ** Check if storage device is mounted **
		if (D) Log.d(TAG, "-> check for storage card");
		String state = Environment.getExternalStorageState();
		if (!state.equals(Environment.MEDIA_MOUNTED)) {
			if (D) Log.e(TAG, "-> media not mounted: " + state);
			if(mHandler != null){
				mHandler.obtainMessage(MESSAGE_STORAGE_DIALOG).sendToTarget();
			}
		}

		// ** Initialize data model with data from database **
		if (D) Log.d(TAG, "-> init data model");
		mModel.setCurrentSurvey(mDatasource.getSurvey());

		// ** Restore Preferences **
		if (D) Log.d(TAG, "-> restore preferences");
		PreferenceManager.setDefaultValues(ApplicationContext.getContext(), R.xml.preferences, false);
	}

	// **************************************************
	// *** Server communication methods
	// **************************************************

	/** Send data to the server. */
	public void sendDataToServer() {
		if (D) Log.d(TAG, "sendDataToServer()");

		if (mModel.getCurrentSurvey() != null) {
			// ** Check survey for completion **
			for (int i = 0; i < mModel.getCurrentSurvey().getQuestions().size(); i++) {
				// ** Copy current question **
				Question question = mModel.getCurrentSurvey().getQuestions().get(i);
				// ** Debug output **
				if (D) Log.d(TAG, "-> question " + question.getContent());
				if (D) Log.d(TAG, "-> is answered: " + question.isAnswered());
				// ** Check if question is answered and visible,
				// empty questions (question id == -1) and invisible questions
				// don't have to be answered, they don't get sent **
				if (!question.isAnswered() && question.getQID() != -1 && question.isVisible()) {
					// ** Send message to class for showing toast **
					if(mHandler != null){
						mHandler.obtainMessage(MESSAGE_TOAST, ApplicationContext.getContext().getResources().getString(R.string.incomplete_survey)).sendToTarget();
					}
					// ** Return if survey hasn't been completed **
					return;
				}
			}
			// ** Execute the Asynctask for sending data to server **
			if(mContext != null){
				new DataSending(mContext, MainController.this, mModel.getCurrentSurvey()).execute();
			}
		} else {
			// ** Show toast if survey is null **
			if(mHandler != null){
				mHandler.obtainMessage(MESSAGE_TOAST, ApplicationContext.getContext().getResources().getString(R.string.no_survey)).sendToTarget();
			}
		}
	}

	/** Receive data from the server. */
	public void getDataFromServer() {
		if (D) Log.d(TAG, "getDataFromServer()");

		// ** Check length of token **
		String token = mSettings.getString("token", "");
		if (token.length() == TOKEN_LENGTH) {
			// ** Execute Asynctask for receiving data from server **
			if(mContext != null){
				new DataReceiving(mContext, MainController.this).execute();
			}
		} else {
			// ** Show token entering dialog if token is to short
			// or not available **
			if(mHandler != null){
				mHandler.obtainMessage(MESSAGE_TOKEN_DIALOG).sendToTarget();
			}
		}
	}

	// **************************************************
	// *** Data management methods
	// **************************************************

	/**
	 * Set current amount of pages in fragment pager to data model.
	 * 
	 * @param amount Current amount of pages in fragment pager
	 */
	public void setPageAmount(int amount){
		if (D) Log.d(TAG, "setPageAmount()");
		
		// ** Set amount to data model **
		mModel.setPageAmount(amount);
	}
	
	/**
	 * Add uri of file to trash uri list which gets deleted on destroy of activity.
	 * 
	 * @param uri Uri of file
	 */
	public void addTrashUriToList(Uri uri){
		if (D) Log.d(TAG, "addTrashUriToList()");
		
		mModel.addTrashUriToList(uri);
	}
	
	/**
	 * Create directory in particular location.
	 * 
	 * @param name
	 *            Name of the directory.
	 * @return Path to directory, empty string if directory making failed
	 * @throws IOException
	 */
	public String makeDir(String name) {
		if (D) Log.d(TAG, "-> makeDir()");

		// ** Create directory if folders do not exist **
		String state = Environment.getExternalStorageState();
		// ** Check if storage card is inserted **
		if (!state.equals(Environment.MEDIA_MOUNTED)) {
			if (D) Log.d(TAG, "-> media not mounted: STATE: " + state);
			return "";
		}
		File folder = new File(Environment.getExternalStorageDirectory(), name);
		if (!folder.exists()) {
			folder.mkdirs();
		}
		return folder.getPath();
	}

	/** Move files from default Android media folder to CASS folder */
	public void moveFiles() {
		if (D) Log.d(TAG, "moveFiles()");
		// ** Execute Asynctask to move files **
		new FileMoving(this, mModel.getCurrentSurvey()).execute();
	}

	/**
	 * Save received survey to model and database, if survey meets certain conditions.
	 * 
	 * @param survey
	 *            Survey to be saved
	 */
	public void saveReceivedData(final Survey survey) {
		if (D) Log.d(TAG, "saveReceivedData()");

		// ** Operate in worker thread **
		mWorkerHandler.post(new Runnable() {
			public void run() {
				synchronized (mModel) {
					if (mModel.getCurrentSurvey() == null) {
						if (D) Log.d(TAG, "-> current survey is null");
						// ** Show toast to inform user about new survey **
						if(mHandler != null){
							mHandler.obtainMessage(MESSAGE_TOAST, ApplicationContext.getContext().getResources().getString(R.string.new_survey)).sendToTarget();
						}
						// ** Save new survey **
						setSurvey(survey);
					} else if (survey != null) {
						if (D) Log.d(TAG, "-> new survey is not null");
						// ** Check survey for certain conditions **
						if (survey.getSID() != mModel.getCurrentSurvey().getSID() || survey.getSurveyCount() != mModel.getCurrentSurvey().getSurveyCount()) {
							// ** Show toast to inform user about new survey **
							if(mHandler != null){
								mHandler.obtainMessage(MESSAGE_TOAST, ApplicationContext.getContext().getResources().getString(R.string.new_survey)).sendToTarget();
							}
							if (survey.getQuestions().isEmpty()) {
								if (D) Log.d(TAG, "-> new survey is empty");
								// ** Show toast to inform user about empty
								// survey
								if(mHandler != null){
									mHandler.obtainMessage(MESSAGE_TOAST, ApplicationContext.getContext().getResources().getString(R.string.survey_empty)).sendToTarget();
								}
							} else {
								if (D) Log.d(TAG, "-> new survey is set to current survey");
								// ** Save new survey **
								setSurvey(survey);
							}
						} else {
							if (D) Log.d(TAG, "-> new survey is old survey");
						}
					}
				}
			}
		});

	}

	/**
	 * Set current survey in model and database.
	 * 
	 * @param survey
	 *            Survey to be saved
	 */
	public void setSurvey(Survey survey) {
		if (D) Log.d(TAG, "setSurvey()");

		// ** Delete old survey **
		deleteSurvey();
		// ** Set new survey **
		mModel.setCurrentSurvey(survey);
		mDatasource.insert(survey);
		if(mHandler != null){
			mHandler.obtainMessage(MESSAGE_UPDATE_LIST).sendToTarget();
		}
	}

	/**
	 * Set current question in database.
	 * 
	 * @param question
	 *            Question to be saved
	 */
	public void setQuestion(Question question) {
		if (D) Log.d(TAG, "setQuestion()");

		// ** Update question in database **
		mDatasource.update(question);
	}

	/** Delete identification number(token). */
	public void deleteToken() {
		if (D) Log.d(TAG, "deleteToken()");

		// ** Delete token in preferences **
		mSettings.edit().putString("token", "").commit();
		// ** Delete current survey, which belongs to
		// the token **
		deleteSurvey();
	}

	/** Delete current survey from model and database. */
	public void deleteSurvey() {
		if (D) Log.d(TAG, "deleteData()");

		if (mModel.getCurrentSurvey() != null) {
			mDatasource.delete(mModel.getCurrentSurvey());
			mModel.setCurrentSurvey(null);
		}
		if(mHandler != null){
			mHandler.obtainMessage(MESSAGE_UPDATE_LIST).sendToTarget();
	}	}

	/**
	 * Delete file or folder from external storage.
	 * 
	 * @param file
	 *            File to be deleted
	 */
	public void deleteFile(final File file) {
		if (D) Log.d(TAG, "deleteFile()");

		// ** Check if storage is available **
		String state = Environment.getExternalStorageState();
		if (state.equals(Environment.MEDIA_MOUNTED)) {
			// ** Use worker thread for operation **
			mWorkerHandler.post(new Runnable() {
				public void run() {
					// ** Delete file **
					if (file.exists()) {
						// ** If file is folder, delete all files in folder **
						if (file.isDirectory()) {
							String[] children = file.list();
							for (int i = 0; i < children.length; i++) {
								new File(file, children[i]).delete();
							}
						}
						file.delete();
					}
				}
			});
		}
	}

	/**
	 * Delete files from Android default media folder.
	 * Relevant files have been copied to CASS folder by then.
	 */
	public void deleteFiles() {
		if (D) Log.d(TAG, "deleteFiles()");

		// ** Use worker thread for operation **
		mWorkerHandler.post(new Runnable() {
			public void run() {
				// ** Delete saved files from Android default media folder. 
				// They are saved in trash uri list of data model **
				for (int i = 0; i < mModel.getTrashUriList().size(); i++) {
					// ** Check if media uri is not null **
					if (mModel.getTrashUriList().get(i) != null) {
						File file = new File(getFilePathByUri(mModel.getTrashUriList().get(i)));
						if (D) Log.d(TAG, "-> file path: "+file.getPath());
						// ** Delete file **
						deleteFile(file);
					}	
				}
				mModel.clearTrashUriList();
			}
		});
	}

	// **************************************************
	// *** Media methods
	// **************************************************

	/** Get time stamp for filename. */
	public String getTimeStamp() {
		if (D) Log.d(TAG, "getTimeStamp()");

		Timestamp timestamp = new Timestamp(Calendar.getInstance().getTime().getTime());
		return new SimpleDateFormat("ddMMyy_hhmmss").format(timestamp);
	}

	/**
	 * Parse uri to file name.
	 * 
	 * @param uri
	 *            Uri to the file or content
	 * @return Name of the file
	 */
	public String getFileNameByUri(Uri uri) {
		if (D) Log.d(TAG, "getFileNameByUri(): " + uri);

		String fileName = "";
		if (uri != null) {
			Uri filePathUri = uri;
			if (uri.getScheme() != null) {
				if (D) Log.d(TAG, "-> URI Scheme: " + uri.getScheme());
				// ** Check if uri is content or file uri **
				if (uri.getScheme().toString().equals("content")) {
					// ** Resolve content from uri **
					Cursor cursor = ApplicationContext.getContext().getContentResolver().query(uri, null, null, null, null);
					// ** Parse file name **
					if (cursor.moveToFirst()) {
						int columnIndex = cursor.getColumnIndexOrThrow(MediaColumns.DATA);
						filePathUri = Uri.parse(cursor.getString(columnIndex));
						fileName = filePathUri.getLastPathSegment().toString();
					}
				} else if (uri.getScheme().toString().equals("file")) {
					// ** Get last segment of path if uri is from file **
					fileName = filePathUri.getLastPathSegment().toString();
				} else {
					// ** Declare file name as "Unknown_file" **
					fileName = "Unknown_file" + filePathUri.getLastPathSegment().toString();
				}
			} else {
				if (D) Log.d(TAG, "-> URI Scheme is null");
			}

		}
		return fileName;
	}

	/**
	 * Parse uri to file path.
	 * 
	 * @param uri
	 *            Uri to the file or content
	 * @return Path of file
	 */
	public String getFilePathByUri(Uri uri) {
		if (D) Log.d(TAG, "getFilePathByUri(): " + uri);

		if (uri != null) {
			if (uri.getScheme() != null) {
				if (D) Log.d(TAG, "-> URI Scheme: " + uri.getScheme());
				// ** Check if uri is content or file uri **
				if (uri.getScheme().toString().equals("content")) {
					// ** Resolve content from uri **
					Cursor cursor = ApplicationContext.getContext().getContentResolver().query(uri, null, null, null, null);
					// ** Parse file path **
					if (cursor.moveToFirst()) {
						int columnIndex = cursor.getColumnIndexOrThrow(MediaColumns.DATA);
						return cursor.getString(columnIndex);
					}
				} else if (uri.getScheme().toString().equals("file")) {
					return uri.getPath();
				}
			} else {
				if (D) Log.d(TAG, "-> URI Scheme is null");
				return uri.getPath();
			}
		}
		return "failed";
	}

	/**
	 * Shrink size of bitmap by file uri.
	 * 
	 * @param uri
	 *            Uri of bitmap to be shrinked
	 * @param widht
	 *            Desired width for shrink
	 * @param height
	 *            Desired height for shrink
	 * @return Shrinked bitmap
	 */
	public Bitmap shrinkBitmap(Uri uri, int width, int height) {
		if (D) Log.d(TAG, "shrinkBitmap(): " + uri);

		// ** Get path of file **
		String path = getFilePathByUri(uri);

		// ** Declare options for shrink process **
		BitmapFactory.Options options = new BitmapFactory.Options();
		options.inJustDecodeBounds = true;
		Bitmap bitmap = BitmapFactory.decodeFile(path, options);
		// ** Declare width and height ratio for shrink process **
		int heightRatio = (int) FloatMath.ceil(options.outHeight / (float) height);
		int widthRatio = (int) FloatMath.ceil(options.outWidth / (float) width);
		// ** Initialize in sample size **
		if (heightRatio > 1 || widthRatio > 1) {
			if (heightRatio > widthRatio) {
				options.inSampleSize = heightRatio;
			} else {
				options.inSampleSize = widthRatio;
			}
		}
		// ** Get bitmap with desired options **
		options.inJustDecodeBounds = false;
		bitmap = BitmapFactory.decodeFile(path, options);

		return bitmap;
	}

	/**
	 * Get right rotation degress for image.
	 * 
	 * @param uri
	 *            Uri of image file
	 * @return Degrees of rotation
	 * @throws IOException
	 */
	public int getImageRotation(Uri uri) throws IOException {
		if (D) Log.d(TAG, "getImageRotation(): " + uri);

		// ** Get path of file **
		String path = getFilePathByUri(uri);
		// ** Create exif interface to get orientation **
		ExifInterface exif = new ExifInterface(path);
		// ** Get rotation in degrees and return **
		int rotation = (int) exifOrientationToDegrees(exif.getAttributeInt(ExifInterface.TAG_ORIENTATION, ExifInterface.ORIENTATION_NORMAL));
		return rotation;
	}

	/**
	 * Calculate rotation degrees from exif orientation.
	 * 
	 * @param exifOrientation
	 *            Int of exif orientation
	 * @return Degrees of rotation
	 */
	private float exifOrientationToDegrees(int exifOrientation) {
		if (D) Log.d(TAG, "exifOrientationToDegrees)");

		if (exifOrientation == ExifInterface.ORIENTATION_ROTATE_90) {
			return 90;
		} else if (exifOrientation == ExifInterface.ORIENTATION_ROTATE_180) {
			return 180;
		} else if (exifOrientation == ExifInterface.ORIENTATION_ROTATE_270) {
			return 270;
		}
		return 0;
	}

	// **************************************************
	// *** Super question methods
	// **************************************************

	/**
	 * Add questions of particular category to question list, so that they are shown,
	 * and removes irrelevant questions.
	 * 
	 * @param category
	 *            Category of questions to be shown
	 * @param quesiton Super question object
	 */
	public void showCategory(int category, Question question) {
		if (D) Log.d(TAG, "showCategory)");

		// ** Search question list for desired category **
		for (int i = 0; i < mModel.getCurrentSurvey().getQuestions().size(); i++) {
			// ** Get category of current question **
			int questionCat = mModel.getCurrentSurvey().getQuestions().get(i).getCategory();
			// ** If question is category 0, don't do anything **
			if(questionCat == 0) continue;
			
			for (int j = 0; j < question.getAnswers().size(); j++) {
				// ** Remove all visible questions == categories of
				// answer != chosen category (!!) **
				if (questionCat == question.getAnswers().get(j).getCategory() && questionCat != category) {
					mModel.getCurrentSurvey().getQuestions().get(i).setVisible(false);
				} else if (questionCat == category) {
					// ** Add all questions with desired category **
					mModel.getCurrentSurvey().getQuestions().get(i).setVisible(true);
					mModel.getCurrentSurvey().getQuestions().get(i).setAnswered(false);
				}
			}
			setQuestion(mModel.getCurrentSurvey().getQuestions().get(i));
		}
	}

	// **************************************************
}
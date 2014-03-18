package fi.metropolia.cass.asyncs;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.BufferedInputStream;
import java.io.BufferedOutputStream;
import java.io.OutputStream;
import java.util.ArrayList;

import android.net.Uri;
import android.os.AsyncTask;
import android.util.Log;
import fi.metropolia.cass.application.ApplicationContext;
import fi.metropolia.cass.controllers.MainController;
import fi.metropolia.cass.models.Question;
import fi.metropolia.cass.models.Survey;

/**
 * This class moves files from the default Android media folder to CASS folder. It extends AsyncTask.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class FileMoving extends AsyncTask<Void, Integer, Boolean> {

	// ** Debugging **
	private final String TAG = this.getClass().getSimpleName();
	private static final boolean D = ApplicationContext.Debug;

	// ** Buffer size for byte reading and writing **
	private static final int BUFFER_SIZE = 2 * 1024;

	// ** Member objects **
	private MainController mController = null;
	private Survey mSurvey = null;
	private String mMediaDir = "";

	/**
	 * Constructor.
	 * 
	 * @param controller
	 *            Object of calling class
	 * @param survey
	 *            Current survey
	 */
	public FileMoving(MainController controller, Survey survey) {
		if (D) Log.d(TAG, "constructor");

		this.mController = controller;
		this.mSurvey = survey;
		this.mMediaDir = mController.makeDir("CASS/CASS Media");
	}
	
	/** Called post execute */
	@Override
	protected void onPostExecute(Boolean result) {
		super.onPostExecute(result);
		if (D) Log.d(TAG, "-- ON POSTEXECUTE --");
		// ** Check for result and inform calling controller **
		if (result) {
			// ** Send success message **
			mController.handleMessage(MainController.MESSAGE_SUCCESS, MainController.RESULT_FILE_MOVING);
		}
	}

	/** Called on execute */
	@Override
	protected Boolean doInBackground(Void... params) {
		if (D) Log.d(TAG, "++ DO IN BACKGROUND ++");

		try {
			moveFiles();
		} catch (Exception e) {
			if (D) Log.e(TAG, e.toString());
			return false;
		}
		return true;
	}

	/**
	 * Move files from default Android media folder to CASS folder.
	 * 
	 * @throws IOException
	 */
	private void moveFiles() throws IOException {
		if (D) Log.d(TAG, "moveFiles()");

		if (mSurvey != null) {
			// ** Copy questions in new array list **
			ArrayList<Question> questions = mSurvey.getQuestions();
			// ** Check questions for type VIDEO and PHOTO,
			// files of type AUDIO are already stored in CASS folder **
			for (int i = 0; i < questions.size(); i++) {
				// ** Check if question is visible and not empty **
				if (questions.get(i).isVisible() && questions.get(i).getQID() != -1) {
					if (questions.get(i).getType() == MainController.VIDEO || questions.get(i).getType() == MainController.PHOTO) {
						// ** Check if selected answer is available **
						if (questions.get(i).getSelectedAnswer() != null) {

							// ** Get uri of file and parse to file path **
							Uri mediaUri = questions.get(i).getSelectedAnswer().getMediaUri();
							String path = mController.getFilePathByUri(mediaUri);

							// ** Move files if exist **
							File file = new File(path);
							if (file.exists()) {
								if (D) Log.d(TAG, "-> move file: " + file.getPath());

								// ** Get input stream from file **
								InputStream fIn = new FileInputStream(file);
								// ** Convert to buffered input stream **
								BufferedInputStream bIn = new BufferedInputStream(fIn, BUFFER_SIZE);

								// ** Define destination and name of new file **
								File outFile = new File(mMediaDir, questions.get(i).getSelectedAnswer().getContent());
								// ** Create output stream for new file **
								OutputStream fOut = new FileOutputStream(outFile);
								// ** Convert to buffered output stream **
								BufferedOutputStream bOut = new BufferedOutputStream(fOut, BUFFER_SIZE);

								// ** Byte array for writing **
								byte[] buffer = new byte[BUFFER_SIZE];

								// ** Get total bytes of file **
								if (D) Log.d(TAG, "-> total bytes: " + bIn.available());

								// ** Read bytes from input stream and write to
								// output stream **
								long bytesRead = 0;
								int n = 0;
								while ((n = bIn.read(buffer, 0, BUFFER_SIZE)) != -1) {
									bOut.write(buffer, 0, n);
									bytesRead += n;
								}
								if (D) Log.d(TAG, "-> bytes read: " + bytesRead);

								// ** Close and clear the streams **
								bOut.flush();
								bOut.close();
								bIn.close();
							}
						}
					}
				}
			}
		}
	}
}

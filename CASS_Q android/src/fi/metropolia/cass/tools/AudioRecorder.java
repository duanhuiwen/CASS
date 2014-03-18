package fi.metropolia.cass.tools;

import java.io.IOException;

import fi.metropolia.cass.application.ApplicationContext;
import android.media.MediaRecorder;
import android.os.Environment;
import android.util.Log;

/**
 * This class records audio.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class AudioRecorder {

	// ** Debugging **
	private final String TAG = this.getClass().getSimpleName();
	private static final boolean D = ApplicationContext.Debug;

	// ** Member objects **
	private MediaRecorder mRecorder = null;

	/**
	 * Constructor.
	 */
	public AudioRecorder() {
		if (D) Log.d(TAG, "constructor");

		// ** Initialize new media recorder **
		this.mRecorder = new MediaRecorder();
	}

	/**
	 * Start audio recording.
	 * 
	 * @param path
	 *            Destination where audio is saved
	 * @throws IOException
	 */
	public void start(String path) throws IOException {
		if (D) Log.d(TAG, "start()");

		// ** Check if storage device is mounted **
		String state = Environment.getExternalStorageState();
		if (!state.equals(android.os.Environment.MEDIA_MOUNTED)) {
			if (D) Log.d(TAG, "-> Storage device not mounted");
			throw new IOException("Storage device not found.");
		}

		// ** Set options and path and start to record **
		mRecorder.setAudioSource(MediaRecorder.AudioSource.MIC);
		mRecorder.setOutputFormat(MediaRecorder.OutputFormat.THREE_GPP);
		mRecorder.setAudioEncoder(MediaRecorder.AudioEncoder.AMR_NB);
		mRecorder.setOutputFile(path);
		mRecorder.prepare();
		mRecorder.start();
	}

	/**
	 * Stop audio recording.
	 */
	public void stop() {
		if (D) Log.d(TAG, "stop()");

		mRecorder.stop();
		mRecorder.release();
	}
}

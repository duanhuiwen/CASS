package fi.metropolia.cass.application;

import android.app.Application;
import android.content.Context;
import android.util.Log;

/**
 * Main CASS application class.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class ApplicationContext extends Application {

	// ** Debugging **
	private final String TAG = this.getClass().getSimpleName();
	public static final boolean Debug = true;

	/** Instance of application class */
	private static ApplicationContext mInstance = null;

	/** Called when the application is first created. */
	@Override
	public void onCreate() {
		super.onCreate();
		if (Debug) Log.d(TAG, "+++ ON CREATE +++");

		// ** Initialize instance **
		mInstance = this;
	}

	/**
	 * @return context of application
	 */
	public static Context getContext() {
		return mInstance.getApplicationContext();
	}
}

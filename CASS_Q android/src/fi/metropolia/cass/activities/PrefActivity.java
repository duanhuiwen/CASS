package fi.metropolia.cass.activities;

import fi.metropolia.cass.application.ApplicationContext;
import fi.metropolia.cass.controllers.MainController;
import fi.metropolia.cass.main.R;
import fi.metropolia.cass.models.DataModel;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.preference.PreferenceActivity;
import android.preference.PreferenceManager;
import android.util.Log;

/**
 * This class the settings menu.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class PrefActivity extends PreferenceActivity {

	// ** Debugging **
	private final String TAG = this.getClass().getSimpleName();
	private static final boolean D = ApplicationContext.Debug;

	/** Called when the activity is first created. */
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		if (D) Log.d(TAG, "+++ ON CREATE +++");

		// ** Get preferences from xml file **
		addPreferencesFromResource(R.xml.preferences);

		// ** Initialize data model **
		DataModel model = DataModel.getInstance();
		// ** Initialize controller **
		final MainController controller = new MainController(model);

		// ** Initialize default preferences **
		final SharedPreferences settings = PreferenceManager.getDefaultSharedPreferences(ApplicationContext.getContext());

		// ** Initialize listener for preference changes **
		SharedPreferences.OnSharedPreferenceChangeListener listener = new SharedPreferences.OnSharedPreferenceChangeListener() {
			public void onSharedPreferenceChanged(SharedPreferences prefs, String key) {
				if (D) Log.d(TAG, "Preferences changed: " + key);

				// ** If question server has changed -> delete survey and token
				if (key.equalsIgnoreCase("question_server") || key.equalsIgnoreCase("token")) {
					controller.deleteSurvey();
					// ** Start MainListActivity **
					Intent intent = new Intent(getBaseContext(), MainListActivity.class);
					startActivity(intent);
					
				} else if(key.equalsIgnoreCase("hide_mode")){
					// ** Start MainListActivity **
					Intent intent = new Intent(getBaseContext(), MainListActivity.class);
					startActivity(intent);
				}
			}
		};
		// ** Register listener **
		settings.registerOnSharedPreferenceChangeListener(listener);
	}
}
package fi.metropolia.cass.activities;

import java.util.ArrayList;

import fi.metropolia.cass.adapters.SwipeAdapter;
import fi.metropolia.cass.application.ApplicationContext;
import fi.metropolia.cass.controllers.MainController;
import fi.metropolia.cass.fragments.*;
import fi.metropolia.cass.main.R;
import fi.metropolia.cass.models.DataModel;
import fi.metropolia.cass.models.Question;
import fi.metropolia.cass.models.Survey;
import android.app.Dialog;
import android.content.Intent;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.support.v4.view.ViewPager;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.Window;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

/**
 * This class displays the question fragments.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class SwipeActivity extends FragmentActivity {

	// ** Debugging **
	private final String TAG = this.getClass().getSimpleName();
	private static final boolean D = ApplicationContext.Debug;

	// ** Member objects **
	private DataModel mModel = null;
	private MainController mController = null;
	private SwipeAdapter mSwipeAdapter = null;
	ViewPager mPager = null;
	private int mPosition;

	/** Called when the activity is first created. */
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		if (D) Log.d(TAG, "+++ ON CREATE +++");

		// ** Set up the window layout **
		requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
		setContentView(R.layout.activity_swipe);
		getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.header);

		// ** Initialize DataModel with singleton instance **
		mModel = DataModel.getInstance();
		// ** Initialize MainController to perform logical program actions **
		mController = new MainController(this, mModel);
		// ** Initialize SwipeAdapter for swiping through fragments **
		mSwipeAdapter = new SwipeAdapter(super.getSupportFragmentManager());
		// ** Initialize ViewPager **
		mPager = (ViewPager) findViewById(R.id.viewpager);
		mPager.setAdapter(mSwipeAdapter);
		
		// Initialize position
		mPosition = this.getIntent().getIntExtra("position", 0);
		//this.getIntent().
		
		// ** Initialize send button with listener for click events **
		Button sendButton = (Button) findViewById(R.id.send);
		sendButton.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				// ** Start MainListActivity with command to
				// send data to server **
				Intent intent = new Intent(getBaseContext(), MainListActivity.class);
				// ** Pass command to send data to server **
				intent.putExtra("sendToServer", true);
				startActivity(intent);
			}
		});
		
		// ** Initialize about button with listener for click events **
		Button	aboutButton = (Button) findViewById(R.id.about);
		aboutButton.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				// ** Show information about application to user **
				showAboutDialog();
			}
		});
	}

	@Override
	public void onResume() {
		super.onResume();
		if (D) Log.d(TAG, "++ ON RESUME ++");

		// ** Create set of fragments **
		createFragments();
	}

	@Override
	public void onDestroy() {
		super.onPause();
		if (D) Log.d(TAG, "--- ON DESTROY ---");

		// ** Move files to CASS folder **
		mController.moveFiles();
	}

	/** Create set of fragments */
	private void createFragments() {
		if (D) Log.d(TAG, "createFragments()");

		// ** Update fragment array in new thread **
		runOnUiThread(new Runnable() {
			public void run() {
				// ** Get current survey from DataModel **
				Survey survey = mModel.getCurrentSurvey();
				if (survey != null) {
					// ** Copy questions of survey in new array **
					ArrayList<Question> questions = survey.getQuestions();
					// ** New array for fragments **
					ArrayList<Fragment> fragments = new ArrayList<Fragment>();
					// ** Get hide mode status from preferences **
					boolean hideMode = PreferenceManager.getDefaultSharedPreferences(ApplicationContext.getContext()).getBoolean("hide_mode", false);
					// ** Count for visible fragments **
					int pageNumber = 0;
					// ** Build fragment for each question **
					for (int i = 0; i < questions.size(); i++) {
						// ** Get current question **
						Question currentQuestion = questions.get(i);
						// ** If hide modus is on, don't add answered questions **/
						if(hideMode){
							if(currentQuestion.isAnswered()){
								continue;
							}
						}
						// ** Check for visibility **
						if (currentQuestion.isVisible()) {
							// ** Increase page number **
							pageNumber++;
							// ** New fragment depending on type **
							Fragment frag = null;
							switch (currentQuestion.getType()) {
							case MainController.OPEN_TEXT:
								frag = new OpenTextFragment();
								break;
							case MainController.OPEN_NUMBER:
								frag = new OpenNumberFragment();
								break;
							case MainController.AUDIO:
								frag = new AudioFragment();
								break;
							case MainController.SINGLE_CHOICE:
								frag = new SingleChoiceFragment();
								break;
							case MainController.SUPER:
								frag = new SuperFragment();
								break;
							case MainController.COMMENT:
								frag = new CommentFragment();
								break;
							case MainController.PHOTO:
								frag = new PhotoFragment();
								break;
							case MainController.VIDEO:
								frag = new VideoFragment();
								break;
							case MainController.SLIDER:
								frag = new SliderFragment();
								break;
							case MainController.MULTIPLE_CHOICE:
								frag = new MultipleChoiceFragment();
								break;
							default:
								showToast(getResources().getString(R.string.unidentified_question));
								return;
							}

							// ** Add index and page number as arguments to fragment **
							Bundle bundle = new Bundle();
							bundle.putInt("index", i);
							bundle.putInt("page_number", pageNumber);
							frag.setArguments(bundle);
							// ** Add fragment to array list **
							fragments.add(frag);
						}
					}
					// ** Add fragments to SwipeAdapter **
					mSwipeAdapter.setFragments(fragments);
					// ** Set position of pager if called by MainListActivity **
					if(mPosition != -1){
						mPager.setCurrentItem(mPosition);
						mPosition = -1;
					}
					// ** Set page amount to model for page number displaying on fragments **
					mController.setPageAmount(pageNumber);
					if(D) Log.d(TAG, "-> position: "+mPosition);
				}
			}
		});
	}

	/**
	 * Show toast on user interface.
	 * 
	 * @param msg
	 *            Text shown on the toast.
	 */
	private void showToast(String msg) {
		if (D) Log.d(TAG, "showToast(): " + msg);

		// ** Setup toast with custom layout **
		LayoutInflater inflater = getLayoutInflater();
		View toastRoot = inflater.inflate(R.layout.toast, null);
		Toast toast = new Toast(this);
		TextView text = (TextView) toastRoot.findViewById(R.id.text);
		text.setText(msg);
		toast.setView(toastRoot);
		toast.setDuration(Toast.LENGTH_SHORT);
		toast.show();
	}
	
	/** Show information about application. */
	private void showAboutDialog() {
		if (D) Log.d(TAG, "showAboutDialog()");

		// ** Setup dialog with custom layout **
		final Dialog dialog = new Dialog(SwipeActivity.this, R.style.themeAboutCustom);
		dialog.setContentView(R.layout.dialog_about);

		final float scale = SwipeActivity.this.getResources().getDisplayMetrics().density;
		dialog.getWindow().setLayout((int) (225.0f * scale + 0.5f), dialog.getWindow().getAttributes().height);
		dialog.setCancelable(true);
		
		dialog.show();
	}
	
	/** Create option menu */
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		if (D) Log.d(TAG, "onCreateOptionMenu()");

		MenuInflater inflater = getMenuInflater();
		inflater.inflate(R.menu.option_menu_settings, menu);
		return true;
	}

	/** Called when option item is selected */
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		if (D) Log.d(TAG, "onOptionsItemSelected()");

		switch (item.getItemId()) {
			// ** Start preferences Activity **
		case R.id.settings:
			Intent settingsActivity = new Intent(getBaseContext(), PrefActivity.class);
			startActivity(settingsActivity);
			return true;
			
		default:
			return super.onOptionsItemSelected(item);
		}
	}
}
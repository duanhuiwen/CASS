package fi.metropolia.cass.activities;

import java.util.ArrayList;

import fi.metropolia.cass.adapters.MainListAdapter;
import fi.metropolia.cass.application.ApplicationContext;
import fi.metropolia.cass.controllers.MainController;
import fi.metropolia.cass.main.R;
import fi.metropolia.cass.models.DataModel;
import fi.metropolia.cass.models.Question;
import fi.metropolia.cass.models.Survey;

import android.app.Dialog;
import android.app.ListActivity;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.preference.PreferenceManager;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

/**
 * This class displays the current question list. It manages user dialogs when receiving or sending data.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class MainListActivity extends ListActivity {

	// ** Debugging **
	private final String TAG = this.getClass().getSimpleName();
	private static final boolean D = ApplicationContext.Debug;

	// ** Member objects **
	private DataModel mModel = null;
	private MainController mController = null;

	/** The Handler that gets information back from the Controller */
	private final Handler mHandler = new Handler() {
		@Override
		public void handleMessage(Message msg) {
			if (D) Log.d(TAG, "handleMessage()");

			switch (msg.what) {
			case MainController.MESSAGE_TOAST:
				showToast((String) msg.obj);
				break;

			case MainController.MESSAGE_TOKEN_DIALOG:
				showTokenDialog();
				break;

			case MainController.MESSAGE_DIALOG:
				showDialog((String) msg.obj);
				break;

			case MainController.MESSAGE_STORAGE_DIALOG:
				showStorageDialog();
				break;

			case MainController.MESSAGE_UPDATE_LIST:
				createQuestionList();
				break;

			default:
				break;
			}
		}
	};

	/** Called when the activity is first created. */
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		if (D) Log.d(TAG, "+++ ON CREATE +++");

		// ** Set up the window layout **
		requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
		setContentView(R.layout.activity_main_list);
		getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.header);

		// ** Initialize objects **
		mModel = DataModel.getInstance();
		mController = new MainController(this, mModel, mHandler);

		// ** Initialize application data **
		mController.init();

		// ** Initialize send button with listener for click events **
		Button sendButton = (Button) findViewById(R.id.send);
		sendButton.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				mController.sendDataToServer();
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

		// ** Get Intent data and check if class was called
		// to send data to server **
		if (D) Log.d(TAG, "intent extra send to server: "+this.getIntent().getBooleanExtra("sendToServer", false));
		if (this.getIntent().getBooleanExtra("sendToServer", false)) {
			mController.sendDataToServer();
		} else {
			// ** Else, get the data from server **
			mController.getDataFromServer();
		}
	}

	@Override
	public void onResume() {
		super.onResume();
		if (D) Log.d(TAG, "++ ON RESUME ++");

		// ** Update user interface **
		createQuestionList();
	}
	
//	@Override
//	public void onConfigurationChanged(Configuration newConfig) {
//	  super.onConfigurationChanged(newConfig);
//	  if (D) Log.d(TAG, "++ ON CONFIGURATIONCHANGED ++");
//
//		// ** Update user interface **
//		createQuestionList();
//	}

	/** Create the question list with data from model */
	private void createQuestionList() {
		if (D) Log.d(TAG, "createQuestionList()");

		runOnUiThread(new Runnable() {
			public void run() {
				// ** Get current survey **
				Survey survey = mModel.getCurrentSurvey();
				// ** Build up list if survey is not null and not empty **
				if (survey != null && !survey.getQuestions().isEmpty()) {
					if (D) Log.d(TAG, "-> ALL: " + mModel.getCurrentSurvey().getQuestions().size());
					// ** Get hide mode status from preferences **
					boolean hideMode = PreferenceManager.getDefaultSharedPreferences(ApplicationContext.getContext()).getBoolean("hide_mode", false);
					// ** New array list for visible questions of survey **
					ArrayList<Question> questions = new ArrayList<Question>();
					for (int i = 0; i < survey.getQuestions().size(); i++) {
						// ** If hide modus is on, don't add answered questions **/
						if(hideMode){
							if(survey.getQuestions().get(i).isAnswered()) continue;
						}
						// ** Copy visible questions to new array **
						if (survey.getQuestions().get(i).isVisible()) {
							questions.add(survey.getQuestions().get(i));
						}
					}
				
					// ** Set ListAdapter with questions of survey **
					setListAdapter(new MainListAdapter(MainListActivity.this, questions));
					// ** Add listener for click events **
					getListView().setOnItemClickListener(new OnItemClickListener() {
						public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
							// ** Switch to SwipeActivity on click **
							Intent swipeIntent = new Intent(getBaseContext(), SwipeActivity.class);
							// ** Pass position to SwipeActivity **
							swipeIntent.putExtra("position", position);
							startActivity(swipeIntent);
						}
					});
				} else {
					if (D) Log.d(TAG, "-> survey is null or empty");
					// ** Initialize ListAdapter with empty list **
					setListAdapter(new MainListAdapter(MainListActivity.this, new ArrayList<Question>()));
				}
			}
		});
	}

	/** Show dialog for entering identification number(token) */
	private void showTokenDialog() {
		if (D) Log.d(TAG, "showTokenDialog()");

		// ** Setup dialog with custom layout **
		final Dialog dialog = new Dialog(MainListActivity.this, R.style.themeDialogCustom);
		dialog.setContentView(R.layout.dialog_token);
		dialog.setTitle(getResources().getString(R.string.no_token));

		final float scale = MainListActivity.this.getResources().getDisplayMetrics().density;
		dialog.getWindow().setLayout((int) (240.0f * scale + 0.5f), dialog.getWindow().getAttributes().height);
		dialog.setCancelable(false);

		// ** Initialize text field for token input **
		final EditText token = (EditText) dialog.findViewById(R.id.edit_token);

		// ** Setup ok button **
		Button ok = (Button) dialog.findViewById(R.id.ok);
		
		ok.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				PreferenceManager.getDefaultSharedPreferences(ApplicationContext.getContext()).edit().putString("token", token.getText().toString()).commit();
				// ** Check if token has correct amount of characters **
				if (token.getText().length() < MainController.TOKEN_LENGTH) {
					showToast(getResources().getString(R.string.invalid_id));
				}
				// ** Receive data from server **
				mController.getDataFromServer();
				dialog.cancel();
			}
		});

		// ** Setup cancel button **
		Button cancel = (Button) dialog.findViewById(R.id.cancel);
		cancel.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				dialog.cancel();
			}
		});
		dialog.show();
	}

	/** Show dialog if storage card is not found */
	private void showStorageDialog() {
		if (D) Log.d(TAG, "showStorageDialog()");

		// ** Setup dialog with custom layout **
		final Dialog dialog = new Dialog(MainListActivity.this, R.style.themeDialogCustom);
		dialog.setContentView(R.layout.dialog_alert);
		dialog.setTitle(getResources().getString(R.string.no_sd));

		final float scale = MainListActivity.this.getResources().getDisplayMetrics().density;
		dialog.getWindow().setLayout((int) (240.0f * scale + 0.5f), dialog.getWindow().getAttributes().height);
		dialog.setCancelable(false);

		// ** Setup ok button **
		Button okButton = (Button) dialog.findViewById(R.id.ok);
		okButton.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				// ** Exit application after user's confirm
				// of noticing **
				finish();
				dialog.cancel();
			}
		});
		dialog.show();
	}

	/**
	 * Show dialog for user interaction
	 * 
	 * @param msg
	 *            Text for the dialog
	 */
	private void showDialog(String msg) {
		if (D) Log.d(TAG, "showDialog(): " + msg);

		// ** Setup dialog with custom layout **
		final Dialog dialog = new Dialog(MainListActivity.this, R.style.themeDialogCustom);
		dialog.setContentView(R.layout.dialog_alert);
		dialog.setTitle(msg);

		final float scale = MainListActivity.this.getResources().getDisplayMetrics().density;
		dialog.getWindow().setLayout((int) (240.0f * scale + 0.5f), dialog.getWindow().getAttributes().height);
		dialog.setCancelable(false);

		// ** Setup ok button **
		Button okButton = (Button) dialog.findViewById(R.id.ok);
		okButton.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				dialog.cancel();
			}
		});
		dialog.show();
	}
	
	/** Show information about application. */
	private void showAboutDialog() {
		if (D) Log.d(TAG, "showAboutDialog()");

		// ** Setup dialog with custom layout **
		final Dialog dialog = new Dialog(MainListActivity.this, R.style.themeAboutCustom);
		dialog.setContentView(R.layout.dialog_about);

		final float scale = MainListActivity.this.getResources().getDisplayMetrics().density;
		dialog.getWindow().setLayout((int) (225.0f * scale + 0.5f), dialog.getWindow().getAttributes().height);
		dialog.setCancelable(true);
		
		dialog.show();
	}

	/** Show dialog for confirmation of token deleting */
	private void showDeleteTokenDialog() {
		if (D) Log.d(TAG, "showDeleteTokenDialog()");

		// ** Setup dialog with custom layout **
		final Dialog dialog = new Dialog(MainListActivity.this, R.style.themeDialogCustom);
		dialog.setContentView(R.layout.dialog_token_delete);
		dialog.setTitle(getResources().getString(R.string.delete_token));

		final float scale = MainListActivity.this.getResources().getDisplayMetrics().density;
		dialog.getWindow().setLayout((int) (240.0f * scale + 0.5f), dialog.getWindow().getAttributes().height);
		dialog.setCancelable(false);

		// ** Setup yes button **
		Button yesButton = (Button) dialog.findViewById(R.id.yes);
		yesButton.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				// ** Delete token and inform user **
				mController.deleteToken();
				showToast(getResources().getString(R.string.token_deleted));
				dialog.cancel();
			}
		});

		// ** Setup no button **
		Button noButton = (Button) dialog.findViewById(R.id.no);
		noButton.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				dialog.cancel();
			}
		});
		dialog.show();
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

	/** Create option menu */
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		if (D) Log.d(TAG, "onCreateOptionMenu()");

		MenuInflater inflater = getMenuInflater();
		inflater.inflate(R.menu.option_menu, menu);
		return true;
	}

	/** Called when option item is selected */
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		if (D) Log.d(TAG, "onOptionsItemSelected()");

		switch (item.getItemId()) {
		// ** Get data from server and refresh user interface **
		case R.id.refresh:
			mController.getDataFromServer();
			return true;
			// ** Delete identification number(token) **
		case R.id.delete_token:
			showDeleteTokenDialog();
			return true;
			// ** Start preferences Activity **
		case R.id.settings:
			Intent settingsActivity = new Intent(getBaseContext(), PrefActivity.class);
			startActivity(settingsActivity);
			return true;
			// ** Open file explorer **
		case R.id.media:
			Intent explorerActivity = new Intent(getBaseContext(), FileExplorerActivity.class);
			startActivity(explorerActivity);
			return true;
			// ** Exit application **
		case R.id.exit:
			finish();
			return true;
			// ** Default **
		default:
			return super.onOptionsItemSelected(item);
		}
	}
}
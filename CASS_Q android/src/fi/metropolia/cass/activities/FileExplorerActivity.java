package fi.metropolia.cass.activities;

import java.io.File;
import java.util.ArrayList;

import fi.metropolia.cass.adapters.ExplorerAdapter;
import fi.metropolia.cass.application.ApplicationContext;
import fi.metropolia.cass.main.R;
import android.app.Dialog;
import android.app.ListActivity;
import android.net.Uri;
import android.os.Bundle;
import android.os.Environment;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.Window;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;
import android.content.Intent;

/**
 * This class displays the list of media files in the CASS Media folder.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class FileExplorerActivity extends ListActivity {

	// ** Debugging **
	private final String TAG = this.getClass().getSimpleName();
	private static final boolean D = ApplicationContext.Debug;

	// ** Member objects **
	private ArrayList<String> mItem = null;
	private ArrayList<String> mPath = null;
	private File mMediaDir = null;
	private TextView mLocationView = null;

	/** Called when the activity is first created. */
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		if (D) Log.d(TAG, "+++ ON CREATE +++");

		// ** Set up the window layout **
		requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
		setContentView(R.layout.activity_file_explorer);
		getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.header);

		// ** Initialize text view for file name **
		mLocationView = (TextView) findViewById(R.id.location);

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

		// ** Check if storage card is inserted **
		String state = Environment.getExternalStorageState();
		if (state.equals(Environment.MEDIA_MOUNTED)) {
			mMediaDir = new File(Environment.getExternalStorageDirectory(), "CASS/CASS Media");
		}
		// ** Check if media folder exists **
		if (mMediaDir != null && mMediaDir.exists()) {
			// ** Make file list **
			makeFileList();
		} else {
			showToast(getResources().getString(R.string.no_media_folder));
		}
	}

	/** Make list of files in folder. */
	private void makeFileList() {
		if (D) Log.d(TAG, "makeFileList()");

		// ** Setup text view for path of folder **
		mLocationView.setText("Location: " + mMediaDir.getPath());

		// ** Initialize new array lists
		// for files and their paths **
		mItem = new ArrayList<String>();
		mPath = new ArrayList<String>();

		// ** Get files of folder **
		File[] files = mMediaDir.listFiles();

		// ** Add files and paths to array lists **
		for (int i = 0; i < files.length; i++) {
			File file = files[i];
			mPath.add(file.getPath());

			if (file.isDirectory()) {
				mItem.add(file.getName() + "/");
			} else {
				mItem.add(file.getName());
			}
		}

		// ** Set list adapter for list view **
		setListAdapter(new ExplorerAdapter(FileExplorerActivity.this, mItem));
	}

	/** Called when a list item is clicked. */
	@Override
	protected void onListItemClick(ListView l, View v, int position, long id) {

		// ** Initialize intent for media file opening **
		Intent intent = new Intent();
		intent.setAction(android.content.Intent.ACTION_VIEW);

		// ** Initialize new file with path of item **
		File file = new File(mPath.get(position));

		// ** Identify type of
		String ext = file.getName().substring(file.getName().indexOf(".") + 1).toLowerCase();
		String type = identifyType(ext);
		String format = type + "/" + ext;
		if (D) Log.d(TAG, "-> file format: " + format);

		// ** Set data and type for content **
		if (type.length() > 0) {
			intent.setDataAndType(Uri.fromFile(file), format);
			startActivity(intent);
		} else {
			showToast(getResources().getString(R.string.invalid_type));
		}
	}

	/**
	 * Identify file type by file name ending.
	 * 
	 * @param ext
	 *            File name ending
	 */
	private String identifyType(String ext) {
		// ** String containing common android image formats **
		String image = "jpg.gif.png.jpeg.bmp.webp";
		// ** String containing common android video formats **
		String video = "mpeg.3gp.mp4";

		// ** Check for ending and return type **
		if (image.contains(ext)) {
			return "image";
		} else if (video.contains(ext)) {
			return "video";
		} else if (ext.equalsIgnoreCase("amr")) {
			return "audio";
		}
		return "";
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
		final Dialog dialog = new Dialog(FileExplorerActivity.this, R.style.themeAboutCustom);
		dialog.setContentView(R.layout.dialog_about);

		final float scale = FileExplorerActivity.this.getResources().getDisplayMetrics().density;
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
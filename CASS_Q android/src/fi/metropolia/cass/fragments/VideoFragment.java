package fi.metropolia.cass.fragments;

import java.io.File;

import fi.metropolia.cass.application.ApplicationContext;
import fi.metropolia.cass.controllers.MainController;
import fi.metropolia.cass.main.R;
import fi.metropolia.cass.models.Answer;
import fi.metropolia.cass.models.DataModel;
import fi.metropolia.cass.models.Question;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.os.Environment;
import android.support.v4.app.Fragment;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;
import android.view.View.OnClickListener;

/**
 * This class displays the video answer page. It starts the Intent to the camera application and manages the replaying of the recorded video.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class VideoFragment extends Fragment {

	// ** Debugging **
	private final String TAG = this.getClass().getSimpleName();
	private static final boolean D = ApplicationContext.Debug;

	// ** Video request code **
	private static final int REQUEST_VIDEO_CAPTURED = 77;

	// ** Member objects **
	private DataModel mModel = null;
	private MainController mController = null;
	private Context mContext = null;
	private LayoutInflater mInflater = null;
	private Question mQuestion = null;
	private Button mPlayButton = null;
	private Button mVideoButton = null;
	private Uri mVideoUri = null;
	private String mFilename = "";

	/**
	 * Constructor. Prepares data model and controller for data access and method calls.
	 */
	public VideoFragment() {
		if (D) Log.d(TAG, "constructor");

		this.mModel = DataModel.getInstance();
		this.mController = new MainController(mModel);
	}

	/** Called when the fragment is first created. */
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
		if (D) Log.d(TAG, "++ ON CREATEVIEW ++");

		// ** Initialize layout **
		View viewHolder = inflater.inflate(R.layout.fragment_video, container, false);

		// ** Initialize Context and Inflater **
		this.mContext = container.getContext();
		this.mInflater = inflater;

		// ** Get index of fragment's question and initialize question **
		int index = this.getArguments().getInt("index");
		if (mModel.getCurrentSurvey().getQuestions().size() >= index) {
			mQuestion = mModel.getCurrentSurvey().getQuestions().get(index);
		} else {
			mQuestion = new Question(-1);
		}

		// ** Get page number **
		int pageNumber = this.getArguments().getInt("page_number");

		// ** Initialize text view for question content **
		TextView dispQuest = (TextView) viewHolder.findViewById(R.id.question);
		dispQuest.setText(mQuestion.getContent());

		// ** Set button to play video **
		mPlayButton = (Button) viewHolder.findViewById(R.id.play_button);
		mPlayButton.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				playVideo();
			}
		});

		// ** Set button to record video **
		mVideoButton = (Button) viewHolder.findViewById(R.id.video);
		mVideoButton.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				recordVideo();
			}
		});

		// ** Initialize page number **
				TextView counter = (TextView) viewHolder.findViewById(R.id.counter);
				counter.setText(pageNumber + "/" + mModel.getPageAmount());

		// ** Restore data when question is answered already **
		if (mQuestion.isAnswered()) {
			mFilename = mQuestion.getSelectedAnswer().getContent();
			mVideoUri = mQuestion.getSelectedAnswer().getMediaUri();
			updateUI();
		}

		return viewHolder;
	}

	/**
	 * Start Intent to camera application.
	 */
	private void recordVideo() {
		if (D) Log.d(TAG, "recordVideo()");

		// ** Initialize and start Intent **
		Intent intent = new Intent(android.provider.MediaStore.ACTION_VIDEO_CAPTURE);
		startActivityForResult(intent, REQUEST_VIDEO_CAPTURED);
	}

	/**
	 * Start Intent to media player.
	 */
	private void playVideo() {
		if (D) Log.d(TAG, "playVideo()");

		// ** File to check if the video file has
		// been copied to the CASS folder already **
		File file = new File(Environment.getExternalStorageDirectory(), "CASS/CASS Media/" + mFilename);
		// ** File to check if the video file is still
		// accessible by the video uri from default Android media folder **
		File uri = new File(mController.getFilePathByUri(mVideoUri));

		Intent intent = null;
		if (file.exists()) {
			if (D) Log.d(TAG, "-> file is already in CASS folder");
			// ** Start Intent with file from CASS folder **
			intent = new Intent(Intent.ACTION_VIEW, Uri.parse(file.getPath()));
			intent.setDataAndType(Uri.fromFile(file), "video/*");
			startActivity(intent);
		} else if (uri.exists()) {
			if (D) Log.d(TAG, "-> file is still in Android media folder");
			// ** Start Intent with file from default Android media folder **
			intent = new Intent(Intent.ACTION_VIEW, mVideoUri);
			startActivity(intent);
		} else {
			if (D) Log.d(TAG, "-> file not found");
			// ** Non of those file exists **
			showToast(ApplicationContext.getContext().getResources().getString(R.string.no_video));
		}
	}

	/** Called when the Intent returns to the application. */
	@Override
	public void onActivityResult(int requestCode, int resultCode, Intent data) {
		super.onActivityResult(requestCode, resultCode, data);
		if (D) Log.d(TAG, "+++ ON ACTIVITYRESULT ++");

		// ** Get result of video recording from camera application **
		if (resultCode == Activity.RESULT_OK) {
			if (requestCode == REQUEST_VIDEO_CAPTURED) {
				// ** Add old uri to trash uri list.
				// File will be deleted later then **
				if (mVideoUri != null) {
					mController.addTrashUriToList(mVideoUri);
				}
				// ** Get data from camera application **
				mVideoUri = data.getData();
				if (mVideoUri != null) {
					if (D) Log.d(TAG, "-> video uri is not null");
					// ** Get time stamp for filename **
					String time = mController.getTimeStamp();
					// ** Parse uri and get type of file by filename ending **
					mFilename = mController.getFileNameByUri(mVideoUri);
					// ** Create new filename **
					mFilename = time + mFilename.substring(mFilename.indexOf("."));
					if (D) Log.d(TAG, "-> filename: " + mFilename);
					if (D) Log.d(TAG, "-> video uri: " + mVideoUri);
				}
				// ** Update user interface **
				updateUI();
				// ** Save question object **
				save();
			}
		} else if (resultCode == Activity.RESULT_CANCELED) {
			if (D) Log.d(TAG, "-> video recording canceled");
			// ** Video recording was canceled **
			mVideoUri = null;
			showToast(ApplicationContext.getContext().getResources().getString(R.string.canceled));
		}
	}

	/**
	 * Update user interface.
	 */
	private void updateUI() {
		if (D) Log.d(TAG, "updateUI()");

		// ** Set preview text view visible and set text **
		if (mVideoUri != null) {
			mPlayButton.setVisibility(View.VISIBLE);
		} else {
			if (D) Log.d(TAG, "-> video uri is null");
		}
	}

	/**
	 * Save question object with answer.
	 */
	private void save() {
		if (D) Log.d(TAG, "save()");

		if (mQuestion.getQID() != -1) {
			// ** Save data to question object **
			if (mFilename.length() > 0 && mVideoUri != null) {
				Answer answer = new Answer(-mQuestion.getQID());
				answer.setRefQID(mQuestion.getQID());
				answer.setContent(mFilename);
				answer.setMediaUri(mVideoUri);
				// ** Remove old answers **
				mQuestion.getAnswers().clear();
				mQuestion.addAnswer(answer);
				mQuestion.setSelectedAID("-" + mQuestion.getQID());
				mQuestion.setAnswered(true);
				mController.setQuestion(mQuestion);
				if (D) Log.d(TAG, "-> question has been saved");
			} else {
				if (D) Log.d(TAG, "-> question has not been answered");
				// ** Question has not been answered **
				mQuestion.setAnswered(false);
			}
		}
	}

	/**
	 * Show toast on user interface.
	 * 
	 * @param msg
	 *            Text shown on the toast.
	 */
	private void showToast(String msg) {
		if (D) Log.d(TAG, "showToast(): " + msg);

		View toastRoot = mInflater.inflate(R.layout.toast, null);
		Toast toast = new Toast(mContext);
		TextView text = (TextView) toastRoot.findViewById(R.id.text);
		text.setText(msg);
		toast.setView(toastRoot);
		toast.setDuration(Toast.LENGTH_SHORT);
		toast.show();
	}
}

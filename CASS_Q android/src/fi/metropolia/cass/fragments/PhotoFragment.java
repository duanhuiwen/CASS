package fi.metropolia.cass.fragments;

import java.io.File;
import java.io.FileNotFoundException;

import fi.metropolia.cass.application.ApplicationContext;
import fi.metropolia.cass.controllers.MainController;
import fi.metropolia.cass.main.R;
import fi.metropolia.cass.models.Answer;
import fi.metropolia.cass.models.DataModel;
import fi.metropolia.cass.models.Question;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.Matrix;
import android.net.Uri;
import android.os.Bundle;
import android.os.Environment;
import android.provider.MediaStore;
import android.support.v4.app.Fragment;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;
import android.view.View.OnClickListener;

/**
 * This class displays the photo answer page. It starts the Intent to the camera application and displays the photo on the user interface.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class PhotoFragment extends Fragment {

	// ** Debugging **
	private final String TAG = this.getClass().getSimpleName();
	private static final boolean D = ApplicationContext.Debug;

	// ** Photo request code **
	private static final int REQUEST_PHOTO_CAPTURED = 55;

	// ** Member objects **
	private DataModel mModel = null;
	private Context mContext = null;
	private MainController mController = null;
	private LayoutInflater mInflater = null;
	private Question mQuestion = null;
	private ImageView mPreview = null;
	private Uri mImageUri = null;
	private File mTempImage = null;
	private String mFilename = "";
	private String mTempDirectory = "";

	/**
	 * Constructor. Prepares data model and controller for data access and method calls.
	 */
	public PhotoFragment() {
		if (D) Log.d(TAG, "constructor");

		this.mModel = DataModel.getInstance();
		this.mController = new MainController(mModel);
	}

	/** Called when the fragment is first created. */
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
		if (D) Log.d(TAG, "++ ON CREATEVIEW ++");

		// ** Initialize layout **
		View viewHolder = inflater.inflate(R.layout.fragment_photo, container, false);

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

		// ** Initialize preview **
		mPreview = (ImageView) viewHolder.findViewById(R.id.preview);

		// ** Set photo button **
		Button photoButton = (Button) viewHolder.findViewById(R.id.photo);
		photoButton.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				takePhoto();
			}
		});

		// ** Initialize page number **
		TextView counter = (TextView) viewHolder.findViewById(R.id.counter);
		counter.setText(pageNumber + "/" + mModel.getPageAmount());

		// ** Make directory for temporary files **
		mTempDirectory = mController.makeDir("CASS/temp");

		// ** Restore data when question is answered already **
		if (mQuestion.isAnswered()) {
			if (D) Log.d(TAG, "Question is answered");
			mFilename = mQuestion.getSelectedAnswer().getContent();
			if (D) Log.d(TAG, "-> restored file name: " + mFilename);
			mImageUri = mQuestion.getSelectedAnswer().getMediaUri();
			if (D) Log.d(TAG, "-> restored image uri: " + mImageUri);
			updateUI();
		}

		return viewHolder;
	}

	/**
	 * Start Intent to camera application.
	 */
	private void takePhoto() {
		if (D) Log.d(TAG, "takePhoto()");

		// ** Initialize and put temporary file to save image **
		Intent intent = new Intent(android.provider.MediaStore.ACTION_IMAGE_CAPTURE);
		// ** Make directory for temporary files **
		mTempDirectory = mController.makeDir("CASS/temp");
		// ** Check if directory is available **
		if (mTempDirectory.length() < 1) {
			showToast(ApplicationContext.getContext().getString(R.string.camera_no_sd));
		} else {
			mTempImage = new File(mTempDirectory, "temp.jpg");
			// ** Check if file has been created correctly **
			if (mTempImage != null) {
				intent.putExtra(MediaStore.EXTRA_OUTPUT, Uri.fromFile(mTempImage));
				// ** start Intent **
				startActivityForResult(intent, REQUEST_PHOTO_CAPTURED);
			} else {
				showToast(ApplicationContext.getContext().getString(R.string.camera_no_media_folder));
			}
		}
	}

	/** Called when the Intent returns to the application. */
	@Override
	public void onActivityResult(int requestCode, int resultCode, Intent data) {
		super.onActivityResult(requestCode, resultCode, data);
		if (D) Log.d(TAG, "+++ ON ACTIVITYRESULT ++");

		// ** Get result of photo taking from camera application **
		if (resultCode == Activity.RESULT_OK) {
			if (requestCode == REQUEST_PHOTO_CAPTURED) {
				// ** Add old uri to trash uri list.
				// File will be deleted later then **
				if (mImageUri != null) {
					mController.addTrashUriToList(mImageUri);
				}
				// ** check if uri is null because
				// camera applications of certain smartphone models
				// don't return an image uri, but save the photo
				// to a predefined file, which is given to the
				// camera when the camera Intent is initialized **
				if (data == null && mTempImage != null) {
					// ** If the returned data is null,
					// we have one of those certain smartphones
					// and use the image of the predefined file **
					if (D) Log.d(TAG, "-> image uri from temp file");
					mImageUri = Uri.fromFile(mTempImage);
				} else {
					// ** Else we use the returned data
					// from the camera application
					// (we don't use the predefined file always,
					// because some models just save thumbnails,
					// which are in low quality) **
					if (D) Log.d(TAG, "-> image uri from camera Intent");
					mImageUri = data.getData();
				}
				if (mImageUri != null) {
					// ** Get time stamp for filename **
					String time = mController.getTimeStamp();
					// ** Parse uri and get type of file by filename ending **
					mFilename = mController.getFileNameByUri(mImageUri);
					// ** Create new filename **
					mFilename = time + mFilename.substring(mFilename.indexOf("."));
					if (D) Log.d(TAG, "-> filename: " + mFilename);
					if (D) Log.d(TAG, "-> image uri: " + mImageUri);
				}
				// ** Update user interface **
				updateUI();
				// ** Save question object **
				save();
			}
		} else if (resultCode == Activity.RESULT_CANCELED) {
			if (D) Log.d(TAG, "-> photo taking canceled");
			// ** Photo taking was canceled **
			mImageUri = null;
			showToast(ApplicationContext.getContext().getString(R.string.canceled));
		}
	}

	/**
	 * Update user interface.
	 */
	private void updateUI() {
		if (D) Log.d(TAG, "updateUI()");

		// ** File to check if the image file has
		// been copied to the CASS folder already **
		String mediaDir = mController.makeDir("CASS/temp");
		if(mediaDir.length() < 1) {
			showToast(ApplicationContext.getContext().getString(R.string.no_sd));
		}
		File file = new File(Environment.getExternalStorageDirectory(), "CASS/CASS Media/" + mFilename);
		// ** File to check if the image file is still
		// accessible by the image uri from default Android media or temp folder
		// **

		File uri = new File(mController.getFilePathByUri(mImageUri));

		try {
			// ** Setup image for preview **
			Bitmap bitmap = null;
			// ** Variable for image rotation **
			int rotation;

			// ** Check if image file does exist in CASS folder **
			if (file.exists()) {
				if (D) Log.d(TAG, "-> file is already in CASS folder");
				// ** If exists, shrink and rotate it **
				bitmap = mController.shrinkBitmap(Uri.fromFile(file), 500, 500);
				rotation = mController.getImageRotation(Uri.fromFile(file));
			} else if (uri.exists()) {
				if (D) Log.d(TAG, "-> file is still in Android media or temp folder");
				// ** Else, get image from image uri,
				// shrink and rotate it **
				bitmap = mController.shrinkBitmap(mImageUri, 500, 500);
				rotation = mController.getImageRotation(mImageUri);
			} else {
				// ** Non of those file exists **
				throw new FileNotFoundException();
			}

			// ** Initialize matrix for image rotation **
			Matrix matrix = new Matrix();
			if (rotation != 0) {
				matrix.postRotate(rotation);
			}

			// ** Create new bitmap out of old bitmap with rotation
			// parameters **
			Bitmap rotatedBmp = Bitmap.createBitmap(bitmap, 0, 0, bitmap.getWidth(), bitmap.getHeight(), matrix, true);

			// ** Set rotated image to preview **
			mPreview.setImageBitmap(rotatedBmp);
			mPreview.setVisibility(View.VISIBLE);
		
		} catch (FileNotFoundException e) {
			if (D) Log.e(TAG, "-> " + e.toString());
			showToast(ApplicationContext.getContext().getString(R.string.photo_not_found));

		} catch (Exception e) {
			if (D) Log.e(TAG, "-> " + e.toString());
			showToast(ApplicationContext.getContext().getString(R.string.no_photo));
		}
	}

	/**
	 * Save question object with answer.
	 */
	private void save() {
		if (D) Log.d(TAG, "save()");

		if (mQuestion.getQID() != -1) {
			// ** Save data to question object **
			if (mFilename.length() > 0 && mImageUri != null) {
				Answer answer = new Answer(-mQuestion.getQID());
				answer.setRefQID(mQuestion.getQID());
				answer.setContent(mFilename);
				if (D) Log.d(TAG, "-> saved file name: " + mFilename);
				answer.setMediaUri(mImageUri);
				if (D) Log.d(TAG, "-> saved image uri: " + mImageUri);
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

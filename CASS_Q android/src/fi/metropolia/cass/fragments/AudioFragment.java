package fi.metropolia.cass.fragments;

import java.io.File;
import java.io.IOException;

import fi.metropolia.cass.application.ApplicationContext;
import fi.metropolia.cass.controllers.MainController;
import fi.metropolia.cass.main.R;
import fi.metropolia.cass.models.Answer;
import fi.metropolia.cass.models.DataModel;
import fi.metropolia.cass.models.Question;
import fi.metropolia.cass.tools.AudioRecorder;

import android.os.SystemClock;
import android.content.Context;
import android.media.MediaPlayer;
import android.media.MediaPlayer.OnCompletionListener;
import android.net.Uri;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.Chronometer;
import android.widget.ProgressBar;
import android.widget.Toast;
import android.widget.Chronometer.OnChronometerTickListener;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.view.View.OnClickListener;

/**
 * This class displays the audio answer page. It starts a audio recorder and player.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class AudioFragment extends Fragment implements Runnable { 

	// ** Debugging **
	private final String TAG = this.getClass().getSimpleName();
	private static final boolean D = ApplicationContext.Debug;

	// ** Member objects **
	private DataModel mModel = null;
	private MainController mController = null;
	private Context mContext = null;
	private LayoutInflater mInflater = null;
	private Question mQuestion = null;
	private Uri mAudioUri = null;
	private MediaPlayer mAudioPlayer = null;
	private AudioRecorder mAudioRecorder = null;
	private Chronometer mStopWatch = null;
	private Button mRecordButton = null;
	private Button mPlayButton = null;
	private RelativeLayout mPlayLayout = null;
	private ProgressBar mProgressBar = null;
	private TextView mPlayTime = null;
	private boolean isRecording = false;
	private boolean isPlaying = false;
	private long mCountUp;
	private String mFilename = "";
	private String mMediaDir = "";;

	/**
	 * Constructor. Prepares data model and controller for data access and method calls.
	 */
	public AudioFragment() {
		if (D) Log.d(TAG, "constructor");

		this.mModel = DataModel.getInstance();
		this.mController = new MainController(mModel);
		this.mMediaDir = mController.makeDir("CASS/CASS Media");
	}

	/** Called when the fragment is first created. */
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
		if (D) Log.d(TAG, "++ ON CREATEVIEW ++");

		// ** Initialize layout **
		View viewHolder = inflater.inflate(R.layout.fragment_audio, container, false);

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

		// ** Initialize text view for play timer **
		mPlayTime = (TextView) viewHolder.findViewById(R.id.play_time);
		mPlayTime.setText(ApplicationContext.getContext().getResources().getString(R.string.audio_start_time));

		// ** Initialize text view for record timer **
		final TextView recordTime = (TextView) viewHolder.findViewById(R.id.record_time);
		recordTime.setText(ApplicationContext.getContext().getResources().getString(R.string.audio_start_time));

		// ** Initialize stop watch **
		mStopWatch = (Chronometer) viewHolder.findViewById(R.id.chrono);
		// ** Set tick listener for stop watch **
		mStopWatch.setOnChronometerTickListener(new OnChronometerTickListener() {
			public void onChronometerTick(Chronometer arg0) {
				mCountUp = (SystemClock.elapsedRealtime() - arg0.getBase()) / 1000;
				String asText;
				if ((mCountUp / 60) < 10 && (mCountUp % 60) < 10) {
					asText = "0" + (mCountUp / 60) + ":0" + (mCountUp % 60);
				} else if ((mCountUp % 60) < 10) {
					asText = (mCountUp / 60) + ":0" + (mCountUp % 60);
				} else if ((mCountUp / 60) < 10) {
					asText = "0" + (mCountUp / 60) + ":" + (mCountUp % 60);
				} else {
					asText = (mCountUp / 60) + ":" + (mCountUp % 60);
				}

				if (isRecording) {
					recordTime.setText(asText);
				} else if (isPlaying) {
					mPlayTime.setText(asText);
				}

			}
		});

		// ** Initialize layout for player interface **
		mPlayLayout = (RelativeLayout) viewHolder.findViewById(R.id.play_layout);

		// ** Initialize progress bar **
		mProgressBar = (ProgressBar) viewHolder.findViewById(R.id.progress_bar);

		// ** Set button to record audio **
		mRecordButton = (Button) viewHolder.findViewById(R.id.record);
		mRecordButton.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				// ** Check if recorder is running **
				if (!isRecording) {
					// ** Start audio recording **
					startRecording();
				} else {
					// ** Stop audio recording **
					stopRecording();
				}
			}
		});

		// ** Set button to play audio **
		mPlayButton = (Button) viewHolder.findViewById(R.id.play);
		mPlayButton.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				// ** Check if player is running **
				if (!isPlaying) {
					// ** Start audio playing **
					startPlaying();
				} else {
					// ** Stop audio playing **
					stopPlaying();
				}
			}
		});

		// ** Initialize page number **
		TextView counter = (TextView) viewHolder.findViewById(R.id.counter);
		counter.setText(pageNumber + "/" + mModel.getPageAmount());
		
		// ** Restore data when question is answered already **
		if (mQuestion.isAnswered()) {
			mFilename = mQuestion.getSelectedAnswer().getContent();
			mAudioUri = mQuestion.getSelectedAnswer().getMediaUri();
			mPlayLayout.setVisibility(View.VISIBLE);
		}

		return viewHolder;
	}

	/**
	 * Start audio recording.
	 */
	private void startRecording() {
		if (D) Log.d(TAG, "startRecording()");

		// ** Add old uri to trash uri list.
		// File will be deleted later then **
		if (mAudioUri != null) {
			mController.addTrashUriToList(mAudioUri);
		}
		// ** Get time stamp for filename **
		String time = mController.getTimeStamp();
		// ** Create new filename **
		mFilename = time + ".amr";
		// ** Reset and start stop watch **
		mStopWatch.setBase(SystemClock.elapsedRealtime());
		mStopWatch.start();
		
		// ** Save question object **
		File file = new File(mMediaDir + "/" + mFilename);
		mAudioUri = Uri.fromFile(file);
		save();

		// ** Initialize audio recorder **
		mAudioRecorder = new AudioRecorder();
		// ** Start audio recording **
		try {
			mAudioRecorder.start(mAudioUri.getPath());
			isRecording = true;
		} catch (IOException e) {
			showToast("Unable to record: " + e.getMessage());
			return;
		}

		// ** Set background of record button **
		mRecordButton.setBackgroundResource(R.drawable.button_audio_record_stop);
	}

	/**
	 * Stop audio recording.
	 */
	private void stopRecording() {
		if (D) Log.d(TAG, "stopRecording()");


		if(mAudioRecorder != null && isRecording){
			// ** Stop stop watch **
			mStopWatch.stop();
			// ** Stop audio recorder **
			mAudioRecorder.stop();
			isRecording = false;
		}
		// ** Show play layout **
		mPlayLayout.setVisibility(View.VISIBLE);
		// ** Set background of record button **
		mRecordButton.setBackgroundResource(R.drawable.button_audio_record);
	}

	/**
	 * Start audio playing.
	 */
	private void startPlaying() {
		if (D) Log.d(TAG, "startPlaying()");

		File file = new File(mAudioUri.getPath());
		if (file.exists()) {

			// ** Reset and start stop watch **
			mStopWatch.setBase(SystemClock.elapsedRealtime());
			mStopWatch.start();
			// ** Initialize audio player **
			mAudioPlayer = new MediaPlayer();
			// ** Set on completion listener to audio player **
			mAudioPlayer.setOnCompletionListener(new OnCompletionListener() {
				public void onCompletion(MediaPlayer mp) {
					isPlaying = false;
					mStopWatch.stop();
					mp.release();
					mp = null;
					mPlayButton.setBackgroundResource(R.drawable.button_audio_play);
				}
			});
			
			// ** Start audio playing **
			try {
				mAudioPlayer.setDataSource(file.getPath());
				mAudioPlayer.prepare();
				// ** Reset progress *
				mProgressBar.setProgress(0);
				// ** Set length of video file to progress bar **
				mProgressBar.setMax(mAudioPlayer.getDuration());
				mAudioPlayer.start();
				isPlaying = true;
				// ** Start update thread **
				new Thread(this).start();
				// ** Set background of play button **
				mPlayButton.setBackgroundResource(R.drawable.button_audio_play_stop);
				
			} catch (IOException e) {
				showToast(ApplicationContext.getContext().getResources().getString(R.string.play_failed) + " " + e.getMessage());
			}
		} else {
			showToast(ApplicationContext.getContext().getResources().getString(R.string.no_audio));
		}
	}

	/**
	 * Stop audio playing.
	 */
	private void stopPlaying() {
		if (D) Log.d(TAG, "stopPlaying()");

		if(mAudioPlayer != null && isPlaying){
			// ** Stop stop watch **
			mStopWatch.stop();
			// ** Stop audio player **
			mAudioPlayer.stop();
			mAudioPlayer.release();
			mAudioPlayer = null;
			isPlaying = false;
		}
		// ** Reset progress *
		mProgressBar.setProgress(0);
		// ** Reset time **
		mPlayTime.setText(ApplicationContext.getContext().getResources().getString(R.string.audio_start_time));
		// ** Set background of play button **
		mPlayButton.setBackgroundResource(R.drawable.button_audio_play);
	}
	
	@Override
	public void onPause(){
		super.onPause();
		if (D) Log.d(TAG, "-- ON PAUSE --");
		
		// ** Stop and release audio recorder and player **
		stopPlaying();
		stopRecording();
	}

	/**
	 * Save question object with answer.
	 */
	private void save() {
		if (D) Log.d(TAG, "save()");

		if (mQuestion.getQID() != -1) {
			// ** Save data to question object **
			if (mFilename.length() > 0 && mAudioUri != null) {
				Answer answer = new Answer(-mQuestion.getQID());
				answer.setRefQID(mQuestion.getQID());
				answer.setContent(mFilename);
				answer.setMediaUri(mAudioUri);
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
	 * Update the progress bar.
	 */
    public void run() {
        int position = 0;
        int duration = mAudioPlayer.getDuration();
        while (mAudioPlayer != null && position < duration) {
            try {
                Thread.sleep(10);
                position = mAudioPlayer.getCurrentPosition();
            } catch (InterruptedException e) {
                return;
            } catch (Exception e) {
                return;
            }            
            mProgressBar.setProgress(position);
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

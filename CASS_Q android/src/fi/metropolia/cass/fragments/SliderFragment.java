package fi.metropolia.cass.fragments;

import fi.metropolia.cass.application.ApplicationContext;
import fi.metropolia.cass.controllers.MainController;
import fi.metropolia.cass.main.R;
import fi.metropolia.cass.models.Answer;
import fi.metropolia.cass.models.DataModel;
import fi.metropolia.cass.models.Question;

import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.SeekBar;
import android.widget.SeekBar.OnSeekBarChangeListener;
import android.widget.TextView;

/**
 * This class displays the slider answer page. It limits the input to a certain range of numbers defined by the question.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class SliderFragment extends Fragment {

	// ** Debugging **
	private final String TAG = this.getClass().getSimpleName();
	private static final boolean D = ApplicationContext.Debug;

	// ** Member objects **
	private DataModel mModel = null;
	private MainController mController = null;
	private Question mQuestion = null;
	private TextView mProgressText = null;
	private int mMin;
	private int mMax;

	/**
	 * Constructor. Prepares data model and controller for data access and method calls.
	 */
	public SliderFragment() {
		if (D) Log.d(TAG, "constructor");

		this.mModel = DataModel.getInstance();
		this.mController = new MainController(mModel);
	}

	/** Called when the fragment is first created. */
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
		if (D) Log.d(TAG, "++ ON CREATEVIEW ++");

		// ** Initialize layout **
		View viewHolder = inflater.inflate(R.layout.fragment_slider, container, false);

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

		// ** Initialize text view for minimum label **
		TextView minText = (TextView) viewHolder.findViewById(R.id.min_label);
		minText.setText(mQuestion.getMinLabel());

		// ** Initialize text view for maximum label **
		TextView maxText = (TextView) viewHolder.findViewById(R.id.max_label);
		maxText.setText(mQuestion.getMaxLabel());

		// ** Get minimum and maximum value from question **
		mMin = mQuestion.getMin();
		mMax = mQuestion.getMax();

		// ** Initialize text view for minimum value **
		TextView minValue = (TextView) viewHolder.findViewById(R.id.min_value);
		minValue.setText(String.valueOf(mMin));

		// ** Initialize text view for maximum value **
		TextView maxValue = (TextView) viewHolder.findViewById(R.id.max_value);
		maxValue.setText(String.valueOf(mMax));

		// ** Initialize text view for progress **
		mProgressText = (TextView) viewHolder.findViewById(R.id.progress);
		mProgressText.setText(Integer.toString(0));
		mProgressText.setText("");

		// ** Initialize slider **
		SeekBar slider = (SeekBar) viewHolder.findViewById(R.id.seek_bar);
		// ** Calculate range of slider
		// because the SeekBar's minimum is always 0 **
		slider.setMax(mMax - mMin);
		slider.setProgress(0);
		slider.setOnSeekBarChangeListener(new OnSeekBarChangeListener() {
			public void onProgressChanged(SeekBar seekBar, int progress, boolean fromUser) {
				// ** Calculate the correct progress **
				mProgressText.setText(Integer.toString(progress + mMin));
			}

			// ** Methods for on seekbar change listener **
			public void onStopTrackingTouch(SeekBar slider) {
			}

			public void onStartTrackingTouch(SeekBar slider) {
			}
		});

		// ** Initialize page number **
		TextView counter = (TextView) viewHolder.findViewById(R.id.counter);
		counter.setText(pageNumber + "/" + mModel.getPageAmount());

		// ** Restore data when question is answered already **
		if (mQuestion.isAnswered()) {
			slider.setProgress(Integer.parseInt(mQuestion.getSelectedAnswer().getContent()) - mMin);
			mProgressText.setText(mQuestion.getSelectedAnswer().getContent());
		}

		return viewHolder;
	}

	/** Called when fragment is paused. */
	@Override
	public void onPause() {
		super.onPause();
		if (D) Log.d(TAG, "-- ON PAUSE --");

		// ** Save question object **
		save();
	}

	/**
	 * Save question object with answer.
	 */
	private void save() {
		if (D) Log.d(TAG, "save()");

		if (mQuestion.getQID() != -1) {
			// ** Save data to question object **
			if (mProgressText.getText().toString().length() > 0) {
				Answer answer = new Answer(-mQuestion.getQID());
				answer.setRefQID(mQuestion.getQID());
				answer.setContent(mProgressText.getText().toString());
				// ** Remove old answers **
				mQuestion.getAnswers().clear();
				mQuestion.addAnswer(answer);
				mQuestion.setSelectedAID("-" + mQuestion.getQID());
				mQuestion.setAnswered(true);
				mController.setQuestion(mQuestion);

			} else {
				mQuestion.setAnswered(false);
			}
		}
	}
}

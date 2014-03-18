package fi.metropolia.cass.fragments;

import java.util.ArrayList;

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
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;

/**
 * This class displays the single choice answer page.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class SingleChoiceFragment extends Fragment {

	// ** Debugging **
	private final String TAG = this.getClass().getSimpleName();
	private static final boolean D = ApplicationContext.Debug;

	// ** Member objects **
	private DataModel mModel = null;
	private MainController mController = null;
	private Question mQuestion = null;
	private RadioGroup mRadioGroup = null;

	/**
	 * Constructor. Prepares data model and controller for data access and method calls.
	 */
	public SingleChoiceFragment() {
		if (D) Log.d(TAG, "constructor");

		this.mModel = DataModel.getInstance();
		this.mController = new MainController(mModel);
	}

	/** Called when the fragment is first created. */
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
		if (D) Log.d(TAG, "++ ON CREATEVIEW ++");

		// ** Initialize layout **
		View viewHolder = inflater.inflate(R.layout.fragment_single_choice, container, false);

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

		// ** Initialize group for radio buttons **
		mRadioGroup = (RadioGroup) viewHolder.findViewById(R.id.radio_group);

		// ** Initialize page number **
		TextView counter = (TextView) viewHolder.findViewById(R.id.counter);
		counter.setText(pageNumber + "/" + mModel.getPageAmount());

		// ** Create the radio buttons **
		if (mQuestion.getQID() != -1) {
			createRadioButtons();
		}

		return viewHolder;

	}

	/**
	 * Create radio buttons dynamically
	 */
	public void createRadioButtons() {

		// ** Copy question's predefined answers to a new array list **
		ArrayList<Answer> answers = mQuestion.getAnswers();

		// ** Setup radio button for every predefined answer **
		for (int i = 0; i < answers.size(); i++) {
			RadioButton radioButton = new RadioButton(ApplicationContext.getContext());
			radioButton.setText(answers.get(i).getContent());
			radioButton.setId(i);
			radioButton.setButtonDrawable(R.drawable.cass_radiobutton);
			radioButton.setTextColor(getResources().getColor(R.color.black));
			radioButton.setTextSize(20);

			// ** Restore data when question is answered already **
			if (mQuestion.isAnswered()) {
				if (answers.get(i).getAID() == Long.parseLong(mQuestion.getSelectedAID())) {
					radioButton.setChecked(true);
				}
			}
			// ** Add radio buttons to group **
			mRadioGroup.addView(radioButton);
		}
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
			// ** Get chosen answer and save it to question object **
			if (mRadioGroup.getCheckedRadioButtonId() != -1) {
				mQuestion.setSelectedAID(mQuestion.getAnswers().get(mRadioGroup.getCheckedRadioButtonId()).getAID() + "");
				mQuestion.setAnswered(true);
				mController.setQuestion(mQuestion);
			}
		}
	}
}

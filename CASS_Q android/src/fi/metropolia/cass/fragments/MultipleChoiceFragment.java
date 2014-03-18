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
import android.widget.CheckBox;
import android.widget.LinearLayout;
import android.widget.TextView;

/**
 * This class displays the multiple choice answer page.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class MultipleChoiceFragment extends Fragment {

	// ** Debugging **
	private final String TAG = this.getClass().getSimpleName();
	private static final boolean D = ApplicationContext.Debug;

	// ** Member objects **
	private DataModel mModel = null;
	private MainController mController = null;
	private Question mQuestion = null;
	private LinearLayout mHoldCheckBoxes = null;

	/**
	 * Constructor. Prepares data model and controller for data access and method calls.
	 */
	public MultipleChoiceFragment() {
		if (D) Log.d(TAG, "constructor");

		this.mModel = DataModel.getInstance();
		this.mController = new MainController(mModel);
	}

	/** Called when the fragment is first created. */
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
		if (D) Log.d(TAG, "++ ON CREATEVIEW ++");

		// ** Initialize layout **
		View viewHolder = inflater.inflate(R.layout.fragment_multiple_choice, container, false);

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

		// ** Initialize layout for the check boxes **
		mHoldCheckBoxes = (LinearLayout) viewHolder.findViewById(R.id.hold_check_boxes);

		// ** Initialize page number **
		TextView counter = (TextView) viewHolder.findViewById(R.id.counter);
		counter.setText(pageNumber + "/" + mModel.getPageAmount());

		// ** Create check boxes **
		if (mQuestion.getQID() != -1) {
			createCheckBoxes();
		}

		return viewHolder;
	}

	/**
	 * Create check boxes dynamically
	 */
	public void createCheckBoxes() {

		// ** Copy question's predefined answers to a new array list **
		ArrayList<Answer> answers = mQuestion.getAnswers();

		// Prepare restoring of data when question is answered already **
		String[] tokens = null;
		if (mQuestion.isAnswered()) {
			String selectedAnswers = mQuestion.getSelectedAID();
			String delims = "[,]";
			tokens = selectedAnswers.split(delims);
		}

		// ** Setup check box for every predefined answer **
		for (int i = 0; i < answers.size(); i++) {
			CheckBox checkBox = new CheckBox(ApplicationContext.getContext());
			checkBox.setText(answers.get(i).getContent());
			checkBox.setId(i);
			checkBox.setButtonDrawable(R.drawable.cass_checkbox);
			checkBox.setTextColor(getResources().getColor(R.color.black));
			checkBox.setTextSize(20);

			// ** Restore data when question is answered already **
			if (tokens != null && tokens[0].length() > 0) {
				for (int j = 0; j < tokens.length; j++) {
					if (answers.get(i).getAID() == Long.parseLong(tokens[j])) {
						checkBox.setChecked(true);
					}
				}
			}
			// ** Add check boxes to layout **
			mHoldCheckBoxes.addView(checkBox);
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
			// ** Get selected answers and save it to question object **
			String selectedAnswers = "";
			// ** Fill string with selected answers **
			for (int i = 0; i < mHoldCheckBoxes.getChildCount(); i++) {
				if (((CheckBox) mHoldCheckBoxes.getChildAt(i)).isChecked()) {
					selectedAnswers += mQuestion.getAnswers().get(i).getAID() + ",";
				}
			}
			// ** Save answer string to question **
			if (selectedAnswers.length() > 0) {
				selectedAnswers = selectedAnswers.substring(0, selectedAnswers.length() - 1);
				mQuestion.setSelectedAID(selectedAnswers);
				mQuestion.setAnswered(true);
				mController.setQuestion(mQuestion);
			}
		}
	}
}

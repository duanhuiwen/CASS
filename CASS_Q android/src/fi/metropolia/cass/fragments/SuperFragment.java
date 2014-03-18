package fi.metropolia.cass.fragments;

import java.util.ArrayList;

import fi.metropolia.cass.activities.SwipeActivity;
import fi.metropolia.cass.application.ApplicationContext;
import fi.metropolia.cass.controllers.MainController;
import fi.metropolia.cass.main.R;
import fi.metropolia.cass.models.Answer;
import fi.metropolia.cass.models.DataModel;
import fi.metropolia.cass.models.Question;

import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.CompoundButton;
import android.widget.CompoundButton.OnCheckedChangeListener;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

/**
 * This class displays the super answer page. It adds and removes certain questions of categories to and from the survey, depending on the selected
 * answer.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class SuperFragment extends Fragment {

	// ** Debugging **
	private final String TAG = this.getClass().getSimpleName();
	private static final boolean D = ApplicationContext.Debug;

	// ** Member objects **
	private DataModel mModel = null;
	private MainController mController = null;
	private LayoutInflater mInflater = null;
	private Context mContext = null;
	private Question mQuestion = null;
	private RadioGroup mRadioGroup = null;
	private int mIndex = 0;
	private int mPageNumber = 0;

	/**
	 * Constructor. Prepares data model and controller for data access and method calls.
	 */
	public SuperFragment() {
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

		// ** Initialize Context and Inflater **
		this.mContext = container.getContext();
		this.mInflater = inflater;

		// ** Get index of fragment's question and initialize question **
		mIndex = this.getArguments().getInt("index");
		if (mModel.getCurrentSurvey().getQuestions().size() >= mIndex) {
			mQuestion = mModel.getCurrentSurvey().getQuestions().get(mIndex);
		} else {
			mQuestion = new Question(-1);
		}

		// ** Get page number **
		mPageNumber = this.getArguments().getInt("page_number");

		// ** Initialize text view for question content **
		TextView dispQuest = (TextView) viewHolder.findViewById(R.id.question);
		dispQuest.setText(mQuestion.getContent());

		// ** Initialize group for radio buttons **
		mRadioGroup = (RadioGroup) viewHolder.findViewById(R.id.radio_group);

		// ** Initialize page number **
		TextView counter = (TextView) viewHolder.findViewById(R.id.counter);
		counter.setText(mPageNumber + "/" + mModel.getPageAmount());

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

			// ** Add on checked change listener to add and remove questions,
			// depending on the selected answer **
			radioButton.setOnCheckedChangeListener(new OnCheckedChangeListener() {
				public void onCheckedChanged(CompoundButton button, boolean isChecked) {
					if (isChecked) {
						mController.showCategory(mQuestion.getAnswers().get(button.getId()).getCategory(), mQuestion);
						showToast(ApplicationContext.getContext().getResources().getString(R.string.new_questions));
						// ** Reload SwipeActivity **
						Intent intent = new Intent(mContext, SwipeActivity.class);
						intent.putExtra("position", mPageNumber-1);
						intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
						startActivity(intent);
					}
				}
			});
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

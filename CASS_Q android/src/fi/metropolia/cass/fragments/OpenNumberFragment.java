package fi.metropolia.cass.fragments;

import fi.metropolia.cass.application.ApplicationContext;
import fi.metropolia.cass.controllers.MainController;
import fi.metropolia.cass.main.R;
import fi.metropolia.cass.models.Answer;
import fi.metropolia.cass.models.DataModel;
import fi.metropolia.cass.models.Question;

import android.content.Context;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

/**
 * This class displays the open number answer page. It limits the input to a certain range of numbers defined by the question.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class OpenNumberFragment extends Fragment {

	// ** Debugging **
	private final String TAG = this.getClass().getSimpleName();
	private static final boolean D = ApplicationContext.Debug;

	// ** Member objects **
	private DataModel mModel = null;
	private MainController mController = null;
	private Context mContext = null;
	private LayoutInflater mInflater = null;
	private Question mQuestion = null;
	private EditText mNumberAnswer = null;

	/**
	 * Constructor. Prepares data model and controller for data access and method calls.
	 */
	public OpenNumberFragment() {
		if (D) Log.d(TAG, "constructor");

		this.mModel = DataModel.getInstance();
		this.mController = new MainController(mModel);
	}

	/** Called when the fragment is first created. */
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
		if (D) Log.d(TAG, "++ ON CREATEVIEW ++");

		// ** Initialize layout **
		View viewHolder = inflater.inflate(R.layout.fragment_open_number, container, false);

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

		// ** Initialize text field for number entering **
		mNumberAnswer = (EditText) viewHolder.findViewById(R.id.number_answer);
		mNumberAnswer.addTextChangedListener(new TextWatcher() {
			public void afterTextChanged(Editable text) {
				try {
					// ** Get minimum and maximum value for range of numbers **
					int min = mQuestion.getMin();
					int max = mQuestion.getMax();
					// ** Check if number is in range **
					int value = Integer.parseInt(text.toString());
					if (value > max || value < min) {
						text.clear();
						// ** Show toast if number is out of range **
						showToast(ApplicationContext.getContext().getString(R.string.invalid_number, min, max));
					}
				} catch (NumberFormatException e) {
					if (D) Log.e(TAG, "-> " + e.toString());
				}
			}

			// ** Methods for text changed listener **
			public void beforeTextChanged(CharSequence s, int start, int count, int after) {
			}

			public void onTextChanged(CharSequence s, int start, int before, int count) {
			}
		});

		// ** Initialize page number **
		TextView counter = (TextView) viewHolder.findViewById(R.id.counter);
		counter.setText(pageNumber + "/" + mModel.getPageAmount());

		// ** Restore data when question is answered already **
		if (mQuestion.isAnswered()) {
			mNumberAnswer.setText(mQuestion.getSelectedAnswer().getContent());
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
			if (mNumberAnswer.getText().toString().length() > 0) {
				Answer answer = new Answer(-mQuestion.getQID());
				answer.setRefQID(mQuestion.getQID());
				answer.setContent(mNumberAnswer.getText().toString());
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

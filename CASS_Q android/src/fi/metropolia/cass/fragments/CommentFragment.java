package fi.metropolia.cass.fragments;

import fi.metropolia.cass.application.ApplicationContext;
import fi.metropolia.cass.controllers.MainController;
import fi.metropolia.cass.main.R;
import fi.metropolia.cass.models.DataModel;
import fi.metropolia.cass.models.Question;

import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

/**
 * This class displays the comment answer page. It limits the input to a certain range of numbers defined by the question.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class CommentFragment extends Fragment {

	// ** Debugging **
	private final String TAG = this.getClass().getSimpleName();
	private static final boolean D = ApplicationContext.Debug;

	// ** Member objects **
	private DataModel mModel = null;
	private MainController mController = null;
	private Question mQuestion = null;

	/**
	 * Constructor. Prepares data model and controller for data access and method calls.
	 */
	public CommentFragment() {
		if (D) Log.d(TAG, "constructor");

		this.mModel = DataModel.getInstance();
		this.mController = new MainController(mModel);
	}

	/** Called when the fragment is first created. */
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
		if (D) Log.d(TAG, "++ ON CREATEVIEW ++");

		// ** Initialize layout **
		View viewHolder = inflater.inflate(R.layout.fragment_comment, container, false);

		// ** Get index of fragment's question and initialize question **
		int index = this.getArguments().getInt("index");
		if (mModel.getCurrentSurvey().getQuestions().size() >= index) {
			mQuestion = mModel.getCurrentSurvey().getQuestions().get(index);
		} else {
			mQuestion = new Question(-1);
		}

		// ** Get page number **
		int pageNumber = this.getArguments().getInt("page_number");

		// ** Initialize text view for comment **
		TextView comment = (TextView) viewHolder.findViewById(R.id.comment);
		comment.setText(mQuestion.getContent());

		// ** Initialize page number **
		TextView counter = (TextView) viewHolder.findViewById(R.id.counter);
		counter.setText(pageNumber + "/" + mModel.getPageAmount());

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
			// ** Set question answered and save question **
			mQuestion.setAnswered(true);
			mController.setQuestion(mQuestion);
		}
	}
}

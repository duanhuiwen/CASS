package fi.metropolia.cass.models;

import java.io.Serializable;
import java.util.ArrayList;

/**
 * This class holds the attributes of the questions.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class Question implements Serializable {
	/** Serial version uid */
	private static final long serialVersionUID = 1L;
	/** ID of question */
	private long qID;
	/** Content of question */
	private String content = "Not available";
	/** Category of question */
	private int category;
	/** Type of question */
	private int type;
	/** Selected answer ID of question */
	private String selectedAID = "";
	/** Answer status of question */
	private boolean answered = false;
	/** Visibility of question */
	private boolean visible = false;
	/** Minimum value of OpenNumberFragment and SliderFragment */
	private int min = 0;
	/** Maximum value of OpenNumberFragment and SliderFragment */
	private int max = 0;
	/** Minimum label of SliderFragment */
	private String minLabel = "";
	/** Maximum label of SliderFragment */
	private String maxLabel = "";
	/** Reference ID of survey */
	private long refSID;
	/** Answers of question */
	private ArrayList<Answer> answers = new ArrayList<Answer>();;

	/**
	 * Constructor.
	 * 
	 * @param qID
	 *            ID of question
	 */
	public Question(long qID) {
		this.qID = qID;
	}

	/**
	 * Add answer to array of answers.
	 * 
	 * @param answer
	 *            Answer of question
	 */
	public void addAnswer(Answer answer) {
		answers.add(answer);
	}

	/**
	 * @return Selected answer ID of question
	 */
	public String getSelectedAID() {
		return selectedAID;
	}

	/**
	 * @param selectedAID
	 *            Selected answer ID of question
	 */
	public void setSelectedAID(String selectedAID) {
		this.selectedAID = selectedAID;
	}

	/**
	 * @return Selected answer of question
	 */
	public Answer getSelectedAnswer() {
		for (int i = 0; i < answers.size(); i++) {
			if (answers.get(i).getAID() == Long.parseLong(selectedAID)) {
				return answers.get(i);
			}
		}
		return null;
	}

	/**
	 * @return Content of question
	 */
	@Override
	public String toString() {
		return content;
	}

	/**
	 * @return Content of question
	 */
	public String getContent() {
		return content;
	}

	/**
	 * @param content
	 *            Content of question
	 */
	public void setContent(String content) {
		this.content = content;
	}

	/**
	 * @return Category of question
	 */
	public int getCategory() {
		return category;
	}

	/**
	 * @param category
	 *            Category of question
	 */
	public void setCategory(int category) {
		this.category = category;
	}

	/**
	 * @return Type of question
	 */
	public int getType() {
		return type;
	}

	/**
	 * @param type
	 *            Type of question
	 */
	public void setType(int type) {
		this.type = type;
	}

	/**
	 * @return Answers of question
	 */
	public ArrayList<Answer> getAnswers() {
		return answers;
	}

	/**
	 * @param answers
	 *            Answers of question
	 */
	public void setAnswers(ArrayList<Answer> answers) {
		this.answers = answers;
	}

	/**
	 * @return ID of question
	 */
	public long getQID() {
		return qID;
	}

	/**
	 * @param qID
	 *            ID of question
	 */
	public void setQID(long qID) {
		this.qID = qID;
	}

	/**
	 * @return Answer status of question
	 */
	public boolean isAnswered() {
		return answered;
	}

	/**
	 * @param answered
	 *            Answer status of question
	 * 
	 */
	public void setAnswered(boolean answered) {
		this.answered = answered;
	}

	/**
	 * @return Reference ID of survey
	 */
	public long getRefSID() {
		return refSID;
	}

	/**
	 * @param refSID
	 *            Reference ID of survey
	 */
	public void setRefSID(long refSID) {
		this.refSID = refSID;
	}

	/**
	 * @return Minimum value of OpenNumberFragment and SliderFragment
	 */
	public int getMin() {
		return min;
	}

	/**
	 * @param min
	 *            Minimum value of OpenNumberFragment and SliderFragment
	 */
	public void setMin(int min) {
		this.min = min;
	}

	/**
	 * @return Maximum value of OpenNumberFragment and SliderFragment
	 */
	public int getMax() {
		return max;
	}

	/**
	 * @param max
	 *            Maximum value of OpenNumberFragment and SliderFragment
	 */
	public void setMax(int max) {
		this.max = max;
	}

	/**
	 * @return Minimum label of OpenNumberFragment and SliderFragment
	 */
	public String getMinLabel() {
		return minLabel;
	}

	/**
	 * @param minLabel
	 *            Minimum label of OpenNumberFragment and SliderFragment
	 */
	public void setMinLabel(String minLabel) {
		this.minLabel = minLabel;
	}

	/**
	 * @return Maximum label of OpenNumberFragment and SliderFragment
	 */
	public String getMaxLabel() {
		return maxLabel;
	}

	/**
	 * @param maxLabel
	 *            Maximum label of OpenNumberFragment and SliderFragment
	 */
	public void setMaxLabel(String maxLabel) {
		this.maxLabel = maxLabel;
	}

	/**
	 * @return Visibility of question
	 */
	public boolean isVisible() {
		return visible;
	}

	/**
	 * @param visible
	 *            Visibility of question
	 */
	public void setVisible(boolean visible) {
		this.visible = visible;
	}
}

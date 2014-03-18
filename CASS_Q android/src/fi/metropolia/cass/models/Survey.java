package fi.metropolia.cass.models;

import java.util.ArrayList;

/**
 * This class holds the attributes of the survey.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class Survey {
	/** ID of survey */
	private long sID;
	/** Name of the user */
	private String userName;
	/** ID of the user */
	private long userID;
	/** Number of current survey */
	private int surveyCount;
	/** Total amount of surveys */
	private int surveyTotal;
	/** Questions of survey */
	private ArrayList<Question> questions = new ArrayList<Question>();

	/**
	 * Constructor.
	 * 
	 * @param sID
	 *            ID of survey
	 */
	public Survey(long sID) {
		this.sID = sID;
	}

	/**
	 * Constructor.
	 */
	public Survey() {
		this.sID = -1;
	}

	/**
	 * Add question to array of questions.
	 * 
	 * @param question
	 *            Question of survey
	 */
	public void addQuestion(Question question) {
		questions.add(question);
	}

	/**
	 * @return Questions of survey
	 */
	public ArrayList<Question> getQuestions() {
		return questions;
	}

	/**
	 * @param questions
	 *            Questions of survey
	 */
	public void setQuestions(ArrayList<Question> questions) {
		this.questions = questions;
	}

	@Override
	public String toString() {
		return "sID:" + sID;
	}

	/**
	 * @return ID of survey
	 */
	public long getSID() {
		return sID;
	}

	/**
	 * @param sID
	 *            ID of survey
	 */
	public void setSID(long sID) {
		this.sID = sID;
	}

	/**
	 * @return Number of current survey
	 */
	public int getSurveyCount() {
		return surveyCount;
	}

	/**
	 * @param surveyCount
	 *            Number of current survey
	 */
	public void setSurveyCount(int surveyCount) {
		this.surveyCount = surveyCount;
	}

	/**
	 * @return Name of the user
	 */
	public String getUserName() {
		return userName;
	}

	/**
	 * @param userName
	 *            Name of the user
	 */
	public void setUserName(String userName) {
		this.userName = userName;
	}

	/**
	 * @return ID of the user
	 */
	public long getUserID() {
		return userID;
	}

	/**
	 * @param userID
	 *            ID of the user
	 */
	public void setUserID(long userID) {
		this.userID = userID;
	}

	/**
	 * @return Total amount of surveys
	 */
	public int getSurveyTotal() {
		return surveyTotal;
	}

	/**
	 * @param surveyTotal
	 *            Total amount of surveys
	 */
	public void setSurveyTotal(int surveyTotal) {
		this.surveyTotal = surveyTotal;
	}
}

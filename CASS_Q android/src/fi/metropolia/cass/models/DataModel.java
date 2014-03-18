package fi.metropolia.cass.models;

import java.util.ArrayList;

import android.net.Uri;

/**
 * This class holds the main data objects, the CASS application is working with.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class DataModel {
	/** Instance of data model */
	private static DataModel instance;
	/** Current survey object */
	private Survey currentSurvey = null;
	/** Current question object */
	private Question currentQuestion = null;
	/** Current answer object */
	private Answer currentAnswer = null;
	/** Current amount of pages in fragment pager */
	private int pageAmount;
	/** List of uris of files to be deleted on destroy of activity */
	ArrayList<Uri> trashUriList = new ArrayList<Uri>();

	/**
	 * Constructor.
	 */
	public DataModel() {
		instance = this;
	}

	/**
	 * @return Instance of data model
	 */
	public static DataModel getInstance() {
		if (instance == null) {
			instance = new DataModel();
		}
		return instance;
	}

	/**
	 * @return Current survey object
	 */
	public Survey getCurrentSurvey() {
		return currentSurvey;
	}

	/**
	 * @param currentSurvey
	 *            Current survey object
	 */
	public void setCurrentSurvey(Survey currentSurvey) {
		this.currentSurvey = currentSurvey;
	}

	/**
	 * @return Current question object
	 */
	public Question getCurrentQuestion() {
		return currentQuestion;
	}

	/**
	 * @param currentQuestion
	 *            Current question object
	 */
	public void setCurrentQuestion(Question currentQuestion) {
		this.currentQuestion = currentQuestion;
	}

	/**
	 * @return Current answer object
	 */
	public Answer getCurrentAnswer() {
		return currentAnswer;
	}

	/**
	 * @param currentAnswer
	 *            Current answer object
	 */
	public void setCurrentAnswer(Answer currentAnswer) {
		this.currentAnswer = currentAnswer;
	}

	/**
	 * @return Current amount of pages in fragment pager
	 */
	public int getPageAmount() {
		return pageAmount;
	}

	/**
	 * @param pageAmount Current amount of pages in fragment pager
	 */
	public void setPageAmount(int pageAmount) {
		this.pageAmount = pageAmount;
	}

	/**
	 * @return List of uris of files to be deleted on destroy of activity
	 */
	public ArrayList<Uri> getTrashUriList() {
		return trashUriList;
	}
	
	/**
	 * @param uri Uri to file that gets deleted
	 */
	public void addTrashUriToList(Uri uri){
		trashUriList.add(uri);
	}
	
	/**
	 * Clear trash uri list
	 */
	public void clearTrashUriList(){
		trashUriList.clear();
	}
	
	
}

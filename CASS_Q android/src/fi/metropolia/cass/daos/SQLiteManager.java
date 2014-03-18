package fi.metropolia.cass.daos;

import java.util.ArrayList;

import fi.metropolia.cass.models.Answer;
import fi.metropolia.cass.models.Question;
import fi.metropolia.cass.models.Survey;

import android.content.ContentValues;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.net.Uri;

/**
 * This class manages all database operations.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class SQLiteManager {
	
	// ** Instance of class **
	private static SQLiteManager instance;
	// ** Keys for table surveys **
	protected static final String TABLE_SURVEYS = "surveys";
	protected static final String COLUMN_SID = "_id";
	protected static final String COLUMN_USERNAME = "userName";
	protected static final String COLUMN_UID = "userID";
	protected static final String COLUMN_SCOUNT = "surveyCount";
	protected static final String COLUMN_STOTAL = "surveyTotal";
	// ** Keys for table questions **
	protected static final String TABLE_QUESTIONS = "questions";
	protected static final String COLUMN_QID = "_id";
	protected static final String COLUMN_QCONTENT = "content";
	protected static final String COLUMN_CATEGORY = "category";
	protected static final String COLUMN_TYPE = "type";
	protected static final String COLUMN_QANSWERED = "answered";
	protected static final String COLUMN_QVISIBLE = "visible";
	protected static final String COLUMN_SELECTEDAID = "selectedAID";
	protected static final String COLUMN_MIN = "min";
	protected static final String COLUMN_MAX = "max";
	protected static final String COLUMN_MINLABEL = "minLabel";
	protected static final String COLUMN_MAXLABEL = "maxLabel";
	protected static final String COLUMN_REFSID = "refSID";
	// ** Keys for table answers **
	protected static final String TABLE_ANSWERS = "answers";
	protected static final String COLUMN_AID = "_aid";
	protected static final String COLUMN_ACONTENT = "content";
	protected static final String COLUMN_AMEDIAURI = "mediaUri";
	protected static final String COLUMN_ACATEGORY = "category";
	protected static final String COLUMN_REFQID = "refQID";

	/**
	 * Constructor.
	 */
	public SQLiteManager() {
		instance = this;
	}

	/**
	 * @return Instance of class
	 */
	public static SQLiteManager getInstance() {
		if (instance == null) {
			instance = new SQLiteManager();
		}
		return instance;
	}

	// **************************************************
	// *** Survey methods
	// **************************************************
	
	/**
	 * Insert new survey into database.
	 * 
	 * @param survey Survey to be inserted
	 */
	public long insert(Survey survey) {
		SQLiteDatabase database = new SQLiteHelper().getWritableDatabase();

		for (int i = 0; i < survey.getQuestions().size(); i++) {
			insert(survey.getQuestions().get(i));
		}

		ContentValues values = new ContentValues();
		values.put(COLUMN_SID, survey.getSID());
		values.put(COLUMN_USERNAME, survey.getUserName());
		values.put(COLUMN_UID, survey.getUserID());
		values.put(COLUMN_SCOUNT, survey.getSurveyCount());
		values.put(COLUMN_STOTAL, survey.getSurveyTotal());
		long num = database.replace(TABLE_SURVEYS, null, values);
		database.close();
		return num;
	}
	
	/**
	 * Update survey in database.
	 * 
	 * @param survey Survey to be updated
	 */
	public long update(Survey survey) {
		SQLiteDatabase database = new SQLiteHelper().getWritableDatabase();

		for (int i = 0; i < survey.getQuestions().size(); i++) {
			update(survey.getQuestions().get(i));
		}

		ContentValues values = new ContentValues();
		values.put(COLUMN_SID, survey.getSID());
		values.put(COLUMN_USERNAME, survey.getUserName());
		values.put(COLUMN_UID, survey.getUserID());
		values.put(COLUMN_SCOUNT, survey.getSurveyCount());
		values.put(COLUMN_STOTAL, survey.getSurveyTotal());
		long num = database.update(TABLE_SURVEYS, values, COLUMN_SID + "=?", new String[] { Long.toString(survey.getSID()) });
		database.close();
		return num;
	}

	/**
	 * Delete survey from database.
	 * 
	 * @param survey Survey to be deleted
	 */
	public void delete(Survey survey) {
		SQLiteDatabase database = new SQLiteHelper().getWritableDatabase();

		for (int i = 0; i < survey.getQuestions().size(); i++) {
			delete(survey.getQuestions().get(i));
		}

		long id = survey.getSID();
		database.delete(TABLE_SURVEYS, COLUMN_SID + " = " + id, null);
		database.close();
	}

	/**
	 * @return Survey
	 */
	public Survey getSurvey() {
		SQLiteDatabase database = new SQLiteHelper().getWritableDatabase();
		Survey survey = null;

		Cursor cursor = database.query(TABLE_SURVEYS, null, null, null, null, null, null);

		cursor.moveToFirst();
		if (!cursor.isAfterLast()) {
			survey = cursorToSurvey(cursor);
			survey.setQuestions(getQuestions(survey.getSID()));
		}
		cursor.close();
		database.close();
		return survey;
	}

	/**
	 * Get all surveys.
	 * 
	 * @return List of surveys
	 */
	public ArrayList<Survey> getAllSurveys() {
		SQLiteDatabase database = new SQLiteHelper().getWritableDatabase();
		ArrayList<Survey> surveys = new ArrayList<Survey>();

		Cursor cursor = database.query(TABLE_SURVEYS, null, null, null, null, null, null);

		cursor.moveToFirst();
		while (!cursor.isAfterLast()) {
			Survey survey = cursorToSurvey(cursor);
			survey.setQuestions(getQuestions(survey.getSID()));
			surveys.add(survey);
			cursor.moveToNext();
		}
		cursor.close();
		database.close();
		return surveys;
	}

	// **************************************************
	// *** Question methods
	// **************************************************
	
	/**
	 * Insert new question into database.
	 * 
	 * @param question Question to be inserted
	 */
	public long insert(Question question) {
		SQLiteDatabase database = new SQLiteHelper().getWritableDatabase();

		for (int i = 0; i < question.getAnswers().size(); i++) {
			insert(question.getAnswers().get(i));
		}

		ContentValues values = new ContentValues();
		values.put(COLUMN_QID, question.getQID());
		values.put(COLUMN_QCONTENT, question.getContent());
		values.put(COLUMN_CATEGORY, question.getCategory());
		values.put(COLUMN_TYPE, question.getType());
		values.put(COLUMN_QANSWERED, new Boolean(question.isAnswered()).toString());
		values.put(COLUMN_QVISIBLE, new Boolean(question.isVisible()).toString());
		values.put(COLUMN_SELECTEDAID, question.getSelectedAID());
		values.put(COLUMN_MIN, question.getMin());
		values.put(COLUMN_MAX, question.getMax());
		values.put(COLUMN_MINLABEL, question.getMinLabel());
		values.put(COLUMN_MAXLABEL, question.getMaxLabel());
		values.put(COLUMN_REFSID, question.getRefSID());
		long num = database.replace(TABLE_QUESTIONS, null, values);
		database.close();
		return num;
	}

	/**
	 * Update question in database.
	 * 
	 * @param question Question to be updated
	 */
	public long update(Question question) {
		SQLiteDatabase database = new SQLiteHelper().getWritableDatabase();

		for (int i = 0; i < question.getAnswers().size(); i++) {
			insert(question.getAnswers().get(i));
		}

		ContentValues values = new ContentValues();
		values.put(COLUMN_QID, question.getQID());
		values.put(COLUMN_QCONTENT, question.getContent());
		values.put(COLUMN_CATEGORY, question.getCategory());
		values.put(COLUMN_TYPE, question.getType());
		values.put(COLUMN_QANSWERED, new Boolean(question.isAnswered()).toString());
		values.put(COLUMN_QVISIBLE, new Boolean(question.isVisible()).toString());
		values.put(COLUMN_SELECTEDAID, question.getSelectedAID());
		values.put(COLUMN_MIN, question.getMin());
		values.put(COLUMN_MAX, question.getMax());
		values.put(COLUMN_MINLABEL, question.getMinLabel());
		values.put(COLUMN_MAXLABEL, question.getMaxLabel());
		values.put(COLUMN_REFSID, question.getRefSID());
		long num = database.update(TABLE_QUESTIONS, values, COLUMN_QID + "=?", new String[] { Long.toString(question.getQID()) });
		database.close();
		return num;
	}

	/**
	 * Delete question from database.
	 * 
	 * @param question Question to be deleted
	 */
	public void delete(Question question) {
		SQLiteDatabase database = new SQLiteHelper().getWritableDatabase();

		for (int i = 0; i < question.getAnswers().size(); i++) {
			delete(question.getAnswers().get(i));
		}

		long id = question.getQID();
		database.delete(TABLE_QUESTIONS, COLUMN_QID + " = " + id, null);
		database.close();
	}
	
	/**
	 * Get questions of certain survey.
	 * 
	 * @param SID Survey ID 
	 * @return List of questions
	 */
	public ArrayList<Question> getQuestions(Long SID) {
		SQLiteDatabase database = new SQLiteHelper().getWritableDatabase();
		ArrayList<Question> questions = new ArrayList<Question>();

		String q = "SELECT * FROM questions WHERE refSID = " + SID + ";";

		Cursor cursor = database.rawQuery(q, null);

		cursor.moveToFirst();
		while (!cursor.isAfterLast()) {
			Question question = cursorToQuestion(cursor);
			question.setAnswers(getAnswers(question.getQID()));
			questions.add(question);
			cursor.moveToNext();
		}
		cursor.close();
		database.close();
		return questions;
	}

	/**
	 * Get all questions
	 * 
	 * @return List of answers
	 */
	public ArrayList<Question> getAllQuestions() {
		SQLiteDatabase database = new SQLiteHelper().getWritableDatabase();
		ArrayList<Question> questions = new ArrayList<Question>();

		Cursor cursor = database.query(TABLE_QUESTIONS, null, null, null, null, null, null);

		cursor.moveToFirst();
		while (!cursor.isAfterLast()) {
			Question question = cursorToQuestion(cursor);
			question.setAnswers(getAnswers(question.getQID()));
			questions.add(question);
			cursor.moveToNext();
		}
		cursor.close();
		database.close();
		return questions;
	}

	// **************************************************
	// *** Answer methods
	// **************************************************
	
	/**
	 * Insert new answer into database.
	 * 
	 * @param answer Answer to be inserted
	 */
	public long insert(Answer answer) {
		SQLiteDatabase database = new SQLiteHelper().getWritableDatabase();
		ContentValues values = new ContentValues();
		values.put(COLUMN_AID, answer.getAID());
		values.put(COLUMN_ACONTENT, answer.getContent());

		if (answer.getMediaUri() != null) {
			values.put(COLUMN_AMEDIAURI, answer.getMediaUri().getPath());
		} else {
			values.put(COLUMN_AMEDIAURI, "");
		}

		values.put(COLUMN_ACATEGORY, answer.getCategory());
		values.put(COLUMN_REFQID, answer.getRefQID());
		;
		long num = database.replace(TABLE_ANSWERS, null, values);
		database.close();
		return num;

	}

	/**
	 * Update answer in database.
	 * 
	 * @param answer Answer to be updated
	 */
	public long update(Answer answer) {
		SQLiteDatabase database = new SQLiteHelper().getWritableDatabase();
		ContentValues values = new ContentValues();
		values.put(COLUMN_AID, answer.getAID());
		values.put(COLUMN_ACONTENT, answer.getContent());

		if (answer.getMediaUri() != null) {
			values.put(COLUMN_AMEDIAURI, answer.getMediaUri().getPath());
		} else {
			values.put(COLUMN_AMEDIAURI, "");
		}

		values.put(COLUMN_ACATEGORY, answer.getCategory());
		values.put(COLUMN_REFQID, answer.getRefQID());
		;
		long num = database.update(TABLE_ANSWERS, values, COLUMN_AID + "=?", new String[] { Long.toString(answer.getAID()) });
		database.close();
		return num;
	}

	/**
	 * Delete answer from database.
	 * 
	 * @param answer Answer to be deleted
	 */
	public void delete(Answer answer) {
		SQLiteDatabase database = new SQLiteHelper().getWritableDatabase();
		long id = answer.getAID();
		database.delete(TABLE_ANSWERS, COLUMN_AID + " = " + id, null);
		database.close();
	}

	/**
	 * Get answer of certain question.
	 * 
	 * @param QID Question ID 
	 * @return List of answers
	 */
	public ArrayList<Answer> getAnswers(Long QID) {
		SQLiteDatabase database = new SQLiteHelper().getWritableDatabase();
		ArrayList<Answer> answers = new ArrayList<Answer>();

		String q = "SELECT * FROM answers WHERE refQID = " + QID + ";";

		Cursor cursor = database.rawQuery(q, null);

		cursor.moveToFirst();
		while (!cursor.isAfterLast()) {
			Answer answer = cursorToAnswer(cursor);
			answers.add(answer);
			cursor.moveToNext();
		}
		cursor.close();
		database.close();
		return answers;
	}

	/**
	 * Get all answers.
	 * 
	 * @return List of answers
	 */
	public ArrayList<Answer> getAllAnswers() {
		SQLiteDatabase database = new SQLiteHelper().getWritableDatabase();
		ArrayList<Answer> answers = new ArrayList<Answer>();

		Cursor cursor = database.query(TABLE_ANSWERS, null, null, null, null, null, null);

		cursor.moveToFirst();
		while (!cursor.isAfterLast()) {
			Answer answer = cursorToAnswer(cursor);
			answers.add(answer);
			cursor.moveToNext();
		}
		cursor.close();
		database.close();
		return answers;
	}

	// **************************************************
	// *** Casting methods
	// **************************************************
	
	/**
	 * Cast cursor to survey.
	 * 
	 * @param cursor Cursor
	 */
	private Survey cursorToSurvey(Cursor cursor) {
		Survey survey = new Survey(cursor.getLong(0));
		survey.setUserName(cursor.getString(1));
		survey.setUserID(cursor.getLong(2));
		survey.setSurveyCount(cursor.getInt(3));
		survey.setSurveyTotal(cursor.getInt(4));
		return survey;
	}

	/**
	 * Cast cursor to question.
	 * 
	 * @param cursor Cursor
	 */
	private Question cursorToQuestion(Cursor cursor) {
		Question question = new Question(cursor.getLong(0));
		question.setContent(cursor.getString(1));
		question.setCategory(cursor.getInt(2));
		question.setType(cursor.getInt(3));
		question.setAnswered(Boolean.parseBoolean(cursor.getString(4)));
		question.setVisible(Boolean.parseBoolean(cursor.getString(5)));
		question.setSelectedAID(cursor.getString(6));
		question.setMin(cursor.getInt(7));
		question.setMax(cursor.getInt(8));
		question.setMinLabel(cursor.getString(9));
		question.setMaxLabel(cursor.getString(10));
		question.setRefSID(cursor.getLong(11));
		return question;
	}

	/**
	 * Cast cursor to answer.
	 * 
	 * @param cursor Cursor
	 */
	private Answer cursorToAnswer(Cursor cursor) {
		Answer answer = new Answer(cursor.getLong(0));
		answer.setContent(cursor.getString(1));
		answer.setMediaUri(Uri.parse(cursor.getString(2)));
		answer.setCategory(cursor.getInt(3));
		answer.setRefQID(cursor.getInt(4));
		return answer;
	}
}

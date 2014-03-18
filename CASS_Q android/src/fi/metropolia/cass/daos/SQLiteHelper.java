package fi.metropolia.cass.daos;

import fi.metropolia.cass.application.ApplicationContext;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.util.Log;

/**
 * This class is a helper for the
 * SQLiteManager class.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class SQLiteHelper extends SQLiteOpenHelper {
	
	// ** Name and version of database **
	private static final String DATABASE_NAME = "cass.db";
	private static final int DATABASE_VERSION = 1;

	/**
	 * Constructor.
	 */
	public SQLiteHelper() {
		super(ApplicationContext.getContext(), DATABASE_NAME, null, DATABASE_VERSION);
	}

	@Override
	public void onCreate(SQLiteDatabase database) {
		// ** Table questions creation **
		final String SURVEYS_CREATE = "create table if not exists " + SQLiteManager.TABLE_SURVEYS + "( " + SQLiteManager.COLUMN_SID + " integer not null primary key, " + SQLiteManager.COLUMN_USERNAME
				+ " text not null, " + SQLiteManager.COLUMN_UID + " integer not null, " + SQLiteManager.COLUMN_SCOUNT + " integer not null, " + SQLiteManager.COLUMN_STOTAL + " integer not null "
				+ ");";
		database.execSQL(SURVEYS_CREATE);

		// ** Table questions creation **
		final String QUESTIONS_CREATE = "create table if not exists " + SQLiteManager.TABLE_QUESTIONS + "( " + SQLiteManager.COLUMN_QID + " integer not null primary key, "
				+ SQLiteManager.COLUMN_QCONTENT + " text, " + SQLiteManager.COLUMN_CATEGORY + " integer, " + SQLiteManager.COLUMN_TYPE + " integer, " + SQLiteManager.COLUMN_QANSWERED
				+ " text not null, " + SQLiteManager.COLUMN_QVISIBLE + " text not null, " + SQLiteManager.COLUMN_SELECTEDAID + " text, " + SQLiteManager.COLUMN_MIN + " integer, "
				+ SQLiteManager.COLUMN_MAX + " integer, " + SQLiteManager.COLUMN_MINLABEL + " text, " + SQLiteManager.COLUMN_MAXLABEL + " text, " + SQLiteManager.COLUMN_REFSID + " integer not null "
				+ ");";
		database.execSQL(QUESTIONS_CREATE);

		// ** Table answers creation **
		final String ANSWERS_CREATE = "create table if not exists " + SQLiteManager.TABLE_ANSWERS + "( " + SQLiteManager.COLUMN_AID + " integer not null primary key, " + SQLiteManager.COLUMN_ACONTENT
				+ " text, " + SQLiteManager.COLUMN_AMEDIAURI + " text, " + SQLiteManager.COLUMN_ACATEGORY + " integer, " + SQLiteManager.COLUMN_REFQID + " integer not null " + ");";
		database.execSQL(ANSWERS_CREATE);
	}

	@Override
	public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
		Log.w(SQLiteHelper.class.getName(), "Upgrading database from version " + oldVersion + " to " + newVersion + ", which will destroy all old data");
		db.execSQL("DROP TABLE IF EXISTS " + SQLiteManager.TABLE_SURVEYS);
		db.execSQL("DROP TABLE IF EXISTS " + SQLiteManager.TABLE_QUESTIONS);
		db.execSQL("DROP TABLE IF EXISTS " + SQLiteManager.TABLE_ANSWERS);
		onCreate(db);
	}

}

package fi.metropolia.cass.models;

import java.io.Serializable;
import android.net.Uri;

/**
 * This class holds the attributes of the answers.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class Answer implements Serializable {
	/** Serial version uid */
	private static final long serialVersionUID = 1L;
	/** ID of answer */
	private long aID;
	/** Content of answer */
	private String content = "Not available";
	/** Media file URI of AudioFragment, PhotoFragment, VideoFragment */
	private Uri mediaUri = null;
	/** Category of answer */
	private int category = 0;
	/** Reference ID of question */
	private long refQID;

	/**
	 * Constructor.
	 * 
	 * @param aID
	 *            ID of answer
	 */
	public Answer(long aID) {
		this.aID = aID;
	}

	/**
	 * Constructor.
	 */
	public Answer() {
	}

	/**
	 * @return Reference ID of question
	 */
	public long getRefQID() {
		return refQID;
	}

	/**
	 * @param refQID
	 *            Reference ID of question
	 */
	public void setRefQID(long refQID) {
		this.refQID = refQID;
	}

	/**
	 * @return Content and answer ID of answer
	 */
	@Override
	public String toString() {
		return content + " aID:" + aID;
	}

	/**
	 * @return Answer ID
	 */
	public long getAID() {
		return aID;
	}

	/**
	 * @param refQID
	 *            Reference ID of question
	 */
	public void setAID(long aID) {
		this.aID = aID;
	}

	/**
	 * @return Content of answer
	 */
	public String getContent() {
		return content;
	}

	/**
	 * @param content
	 *            Content of answer
	 */
	public void setContent(String content) {
		this.content = content;
	}

	/**
	 * @return Media file URI of AudioFragment, PhotoFragment, VideoFragment
	 */
	public Uri getMediaUri() {
		return mediaUri;
	}

	/**
	 * @param mediaUri
	 *            Media file URI of AudioFragment, PhotoFragment, VideoFragment
	 */
	public void setMediaUri(Uri mediaUri) {
		this.mediaUri = mediaUri;
	}

	/**
	 * @return Category of answer
	 */
	public int getCategory() {
		return category;
	}

	/**
	 * @param category
	 *            Category of answer
	 */
	public void setCategory(int category) {
		this.category = category;
	}

}

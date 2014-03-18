package fi.metropolia.cass.controllers;

/**
 * This interface class defines the methods which have to be implemented in controller classes.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public interface IController {

	/**
	 * Handle messages from calling classes
	 * 
	 * @param what
	 *            Code of message
	 * @param result
	 *            Code of result
	 * @param data
	 *            A data object
	 * @param message
	 *            Message to the controller
	 * @param option
	 *            An option in boolean
	 */
	public boolean handleMessage(int what, int result, Object data, String message, boolean option);

	/**
	 * Handle messages from calling classes
	 * 
	 * @param what
	 *            Code of message
	 * @param result
	 *            Code of result
	 * @param data
	 *            A data object
	 * @param option
	 *            An option in boolean
	 */
	public boolean handleMessage(int what, int result, Object data, boolean option);

	/**
	 * Handle messages from calling classes
	 * 
	 * @param what
	 *            Code of message
	 * @param result
	 *            Code of result
	 */
	public boolean handleMessage(int what, int result);

	/**
	 * Handle messages from calling classes
	 * 
	 * @param what
	 *            Code of message
	 * @param result
	 *            Code of result
	 * @param data
	 *            A data object
	 */
	public boolean handleMessage(int what, int result, Object data);

	/**
	 * Handle messages from calling classes
	 * 
	 * @param what
	 *            Code of message
	 * @param data
	 *            A data object
	 * @param option
	 *            An option in boolean
	 */
	public boolean handleMessage(int what, Object data, boolean option);

	/**
	 * Handle messages from calling classes
	 * 
	 * @param what
	 *            Code of message
	 * @param data
	 *            A data object
	 */
	public boolean handleMessage(int what, Object data);

	/**
	 * Handle messages from calling classes
	 * 
	 * @param what
	 *            Code of message
	 * @param result
	 *            Code of result
	 * @param message
	 *            Message to the controller
	 */
	public boolean handleMessage(int what, int result, String message);

	/**
	 * Handle messages from calling classes
	 * 
	 * @param what
	 *            Code of message
	 * @param message
	 *            Message to the controller
	 */
	public boolean handleMessage(int what, String message);

}

package fi.metropolia.cass.controllers;

/**
 * This abstract class is used as the super class for controller classes.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
abstract class Controller implements IController {

	// ** Default constructor **
	public Controller() {
	}

	// ** Dispose method **
	public void dispose() {
	}

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
	abstract public boolean handleMessage(int what, int result, Object data, String message, boolean option);

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
	public boolean handleMessage(int what, Object data, boolean option) {
		return handleMessage(what, -1, data, "", option);
	}

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
	public boolean handleMessage(int what, int result, Object data, boolean option) {
		return handleMessage(what, result, data, "", option);
	}

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
	public boolean handleMessage(int what, int result, Object data) {
		return handleMessage(what, result, data, "", false);
	}

	/**
	 * Handle messages from calling classes
	 * 
	 * @param what
	 *            Code of message
	 * @param data
	 *            A data object
	 */
	public boolean handleMessage(int what, Object data) {
		return handleMessage(what, -1, data, "", false);
	}

	/**
	 * Handle messages from calling classes
	 * 
	 * @param what
	 *            Code of message
	 */
	public boolean handleMessage(int what) {
		return handleMessage(what, -1, null, "", false);
	}

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
	public boolean handleMessage(int what, int result, String message) {
		return handleMessage(what, result, null, message, false);
	}

	/**
	 * Handle messages from calling classes
	 * 
	 * @param what
	 *            Code of message
	 * @param message
	 *            Message to the controller
	 */
	public boolean handleMessage(int what, String message) {
		return handleMessage(what, -1, null, message, false);
	}

	/**
	 * Handle messages from calling classes
	 * 
	 * @param what
	 *            Code of message
	 * @param result
	 *            Code of result
	 */
	public boolean handleMessage(int what, int result) {
		return handleMessage(what, result, null, "", false);
	}

}

package fi.metropolia.cass.adapters;

import java.util.ArrayList;

import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentStatePagerAdapter;

/**
 * This class is the pager for the swipe activity.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class SwipeAdapter extends FragmentStatePagerAdapter {

	// ** List of fragments **
	private ArrayList<Fragment> mFragments = new ArrayList<Fragment>();

	/**
	 * Constructor.
	 * 
	 * @param fm
	 *            FragmentManager
	 */
	public SwipeAdapter(FragmentManager fm) {
		super(fm);
	}

	/**
	 * @param fragments
	 *            List of fragments
	 */
	public void setFragments(ArrayList<Fragment> fragments) {
		this.mFragments = fragments;
	}

	/**
	 * @param f
	 *            Fragment
	 */
	public void addFragment(Fragment f) {
		mFragments.add(f);
	}

	/**
	 * @param f
	 *            Fragment
	 */
	public void removeFragement(Fragment f) {
		mFragments.remove(f);
	}

	/**
	 * @return List of fragments
	 */
	public ArrayList<Fragment> getFragments() {
		return mFragments;
	}

	/**
	 * @param index
	 *            Index of fragment in list
	 * @param f
	 *            Fragment
	 */
	public void addFragment(int index, Fragment f) {
		mFragments.add(index, f);
	}

	/**
	 * @param position
	 *            Index of fragment to be removed
	 */
	public void removeFragement(int position) {
		mFragments.remove(mFragments.get(position));
	}

	@Override
	public Fragment getItem(int position) {
		return this.mFragments.get(position);
	}

	@Override
	public int getCount() {
		return this.mFragments.size();
	}
}

package fi.metropolia.cass.adapters;

import java.util.ArrayList;

import fi.metropolia.cass.main.R;
import fi.metropolia.cass.models.Question;
import android.app.Activity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.TextView;

/**
 * This class creates the list view for the main list activity.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class MainListAdapter extends ArrayAdapter<Question> {

	// ** Member objects **
	private final Activity mContext;
	private final ArrayList<Question> mQuestions;

	/**
	 * Class that holds the text and image for the list items.
	 */
	static class ViewHolder {
		public TextView text;
		public ImageView image;
	}

	/**
	 * Constructor
	 * 
	 * @param context
	 *            Context of activity
	 * @param questions
	 *            Questions to be shown in list
	 */
	public MainListAdapter(Activity context, ArrayList<Question> questions) {
		super(context, R.layout.list_item_main_list, questions);

		// ** Initialize objects **
		this.mContext = context;
		this.mQuestions = questions;
	}

	/** Create view */
	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		// ** Copy view **
		View rowView = convertView;

		// ** Setup layout **
		if (rowView == null) {
			LayoutInflater inflater = mContext.getLayoutInflater();
			rowView = inflater.inflate(R.layout.list_item_main_list, null);
			ViewHolder viewHolder = new ViewHolder();
			viewHolder.text = (TextView) rowView.findViewById(R.id.question);
			viewHolder.image = (ImageView) rowView.findViewById(R.id.status);
			rowView.setTag(viewHolder);
		}

		// ** Initialize view holder **
		ViewHolder holder = (ViewHolder) rowView.getTag();

		// ** Fill text with content and set image **
		holder.text.setText(mQuestions.get(position).getContent());
		if (mQuestions.get(position).isAnswered()) {
			holder.image.setImageResource(R.drawable.answered);
		} else {
			holder.image.setImageResource(R.drawable.not_answered);
		}

		return rowView;
	}
}

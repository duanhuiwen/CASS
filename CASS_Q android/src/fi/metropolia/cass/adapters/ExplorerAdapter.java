package fi.metropolia.cass.adapters;

import java.util.ArrayList;

import fi.metropolia.cass.main.R;
import android.app.Activity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.TextView;

/**
 * This class creates the list view for the explorer activity.
 * 
 * @author Lukas Loechte
 * @author Sebastian Stellmacher
 * @version 1.0 / July 2012
 */
public class ExplorerAdapter extends ArrayAdapter<String> {

	// ** Member objects **
	private final Activity mContext;
	private final ArrayList<String> mItems;

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
	 * @param items
	 *            Items to be shown in list
	 */
	public ExplorerAdapter(Activity context, ArrayList<String> items) {
		super(context, R.layout.list_item_file_explorer, items);

		// ** Initialize objects **
		this.mContext = context;
		this.mItems = items;
	}

	/** Create view */
	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		// ** Copy view **
		View rowView = convertView;

		// ** Setup layout **
		if (rowView == null) {
			LayoutInflater inflater = mContext.getLayoutInflater();
			rowView = inflater.inflate(R.layout.list_item_file_explorer, null);
			ViewHolder viewHolder = new ViewHolder();
			viewHolder.text = (TextView) rowView.findViewById(R.id.file);
			viewHolder.image = (ImageView) rowView.findViewById(R.id.icon);
			rowView.setTag(viewHolder);
		}

		// ** Initialize view holder and set text **
		ViewHolder holder = (ViewHolder) rowView.getTag();
		holder.text.setText(mItems.get(position));

		// ** Set images **
		setTypeIcon(holder, mItems.get(position));

		return rowView;
	}

	/**
	 * Set icon images for files.
	 * 
	 * @param holder
	 *            ViewHolder that holds the text view and image
	 */
	private void setTypeIcon(ViewHolder holder, String item) {

		String ext = item.substring(item.indexOf(".") + 1).toLowerCase();
		// ** String containing common android image formats **
		String image = "jpg.gif.png.jpeg.bmp.webp";
		// ** String containing common android video formats **
		String video = "mpeg.3gp.mp4";

		// ** Set image depending of file type **
		if (image.contains(ext)) {
			holder.image.setBackgroundResource(R.drawable.cass_file_image);
		} else if (video.contains(ext)) {
			holder.image.setBackgroundResource(R.drawable.cass_file_video);
		} else if (ext.equalsIgnoreCase("amr")) {
			holder.image.setBackgroundResource(R.drawable.cass_file_audio);
		}
	}
}

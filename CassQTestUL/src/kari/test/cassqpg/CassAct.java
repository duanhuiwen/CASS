package kari.test.cassqpg;

import android.os.Bundle;
import android.app.Activity;
import org.apache.cordova.*;

public class CassAct extends DroidGap {

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        super.loadUrl("file:///android_asset/www/index.html");
    }


}

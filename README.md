# pdxchambers-weather
WordPress widget that displays weather information.

## License
This PDXChambers Weather is released under the terms of the [GNU Public License (GPL) Version 3.0.](https://www.gnu.org/licenses/gpl-3.0.en.html)
A copy of the license is also located in the LICENSE file in this repo.

## Developer Notes
PDXChambers Weather uses the Open Weather API to pull local weather information and display it as a widget. It actually has two components, the first is the main widget that displays temperature, barometric pressure, and humidity along with a short description of current conditions. It bases that information off a country code and zip/postal code. It also displays an icon as a graphical representation of current conditions.

The second component is a simple temperature display at the top of the page which show the current temperature in the selected location.

## Known Issues
The widget searches for weather information based on city name. Due to API limitations there is an issue for cities with the same name but different locales. The API doesn't accept city,state/province in the search string so it is not possible to search for something like "Portland,OR". This means there is ambiguity if you search for "Portland" since it is unclear if the resulting data is for "Portland, OR" or "Portland, ME". This may not get fixed as the other options for pulling weather data from the API requires one to have a city code (from Open Weather) or the Latitude/Longitude coortinates to get the weather data. Neither of those is impossible to obtain, but the point of this widget is to be as user friendly as possible.

## Installing
1. Download the Zip file from [https://github.com/pdxchambers/pdxchambers-weather](https://github.com/pdxchambers/pdxchambers-weather)
2. Log into WordPress and navigae to Plugins -> Add New
3. Click the Upload Plugin button
4. Click "Choose File" and select the ZIP file you just downloaded
5. Click "Install Now"
6. Navigate to Plugins -> Installed Plugins
7. Find the new plugin, and click the "Activate" link.
8. Go to Appearance -> Widgets and you should see the PDXChambers Weather widget available. Add and configure just like you would any other widget.

## Widget Settings

Here is a breakdown of the widget settings:
- **Title** - This is the title that will be displayed at the top of the widget.
- **Zip/Postal Code** - The local postal code for the area the weather data should be pulled for. Defaults to Lake Oswego, OR USA.
- **Country Code** - The country code for the country the postal code lies in. Defaults to US.
- **Select Temperature Units** - Select between imperial (Farenheit), metric (Celcius), or Kelvin. Default is Farenheit.
- **Display Current temperature at top of page** - Choose whether or not to display current temperature at the top of the page above the header.
- **Display widget** - Choose whether or not to display the main widget.
- **Open Weather Map Application Key** - This is the application key provided by [Open Weather](https://openweathermap.org), it is required for the widget to work.

## Open Weather API Key
This widget relies on the [Open Weather API](https://openweathermap.org/api) to function. The widget won't work without an Application key supplied by Open Weather. To get the key just visit the [Open Weather start page](https://openweathermap.org/appid) to get instructions on how to sign up for a key. Once you have the key, just copy and paste it into the Application Key field in the widget settings. If you miss this step you'll get errors and a 401 - Forbidden code from the server when the widget tries to access the API.
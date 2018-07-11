<?php

// Version v1.0
// CE SCRIPT EXPERIMENTAL REALISE EN PHP PERMET DE DETERMINER LA POSITION DU SOLEIL (AZIMUT + ELEVATION, ...)
// L'ALGORYTHME EST BASÃ‰ SUR UNE Ã‰TUDE FAITE PAR LE National Oceanic and Atmospheric Administration

// LES VARIABLES DE LA BOX EEDOMUS:
// [VAR1] = Latitude (+ => N)
// [VAR2] = Longitude (+ => E)

// Les latitude et longitude doivent contenir des "." (PAS DE VIRGULE)
// EXEMPLE: Latitude = 48.858346
//          Longitude = 2.294496
// OU
//
// EXEMPLE: Latitude = 48.387942
//          Longitude = -4.484993

// EXEMPLE APPEL DE SCRIPT avec variables: http://localhost/script/?exec=position_soleil.php&latitude=[VAR1]&longitude=[VAR2]

// LE RESULTAT EST SOUS FORME XML
// L'ELEVATION CORRESPOND A L'ANGLE ENTRE L'HORIZON ET LE SOLEIL
// XPATH ELEVATION: /Data/Soleil/Solar_Elevation_corrected_for_atm_refraction_deg

// L'AZIMUT CORRESPOND A L'ANGLE ENTRE LE NORD ET LE SOLEIL
// XPATH AZIMUT: /Data/Soleil/Solar_Azimuth_round
// 0°/ 360° = NORD
// 90° = EST
// 180° = SUD
// 270° = OUEST

//--------------------------------------------------------------

// Stocker les variables passÃ©es en argument
$ma_latitude = getArg('latitude');
$ma_longitude = getArg('longitude');

$time_zone = substr(date('O'), -4, 4);
$time_zone = $time_zone / 100;

$diff_jour = strtotime(date('Y')."-".date('m')."-".date('d')." 00:00:00");

//--------------------------------------------------------------

$heure_secondes = date('H') * 3600;
$minutes_secondes = date('i') * 60;
$heure_secondes = $heure_secondes + $minutes_secondes + date('s');

$Time_past_local_midnight = $heure_secondes/ 86400;

$Julian_Day =  $diff_jour / 86400 + 2440587.5 + $Time_past_local_midnight;

$Julian_Century = $Julian_Day - 2451545;
$Julian_Century = $Julian_Century / 36525;

$Geom_Mean_Long_Sun_deg_1 = $Julian_Century * '0.0003032';
$Geom_Mean_Long_Sun_deg_1 = '36000.76983' + $Geom_Mean_Long_Sun_deg_1;
$Geom_Mean_Long_Sun_deg_1 = $Julian_Century * $Geom_Mean_Long_Sun_deg_1;
$Geom_Mean_Long_Sun_deg = fmod('280.46646' + $Geom_Mean_Long_Sun_deg_1,360);

$Geom_Mean_Anom_Sun_deg = '0.0001537' * $Julian_Century;
$Geom_Mean_Anom_Sun_deg = '35999.05029' - $Geom_Mean_Anom_Sun_deg;
$Geom_Mean_Anom_Sun_deg = $Julian_Century * $Geom_Mean_Anom_Sun_deg;
$Geom_Mean_Anom_Sun_deg = '357.52911' + $Geom_Mean_Anom_Sun_deg;

$Eccent_Earth_Orbit = '0.0000001267'*$Julian_Century;
$Eccent_Earth_Orbit = '0.000042037' + $Eccent_Earth_Orbit;
$Eccent_Earth_Orbit = $Julian_Century * $Eccent_Earth_Orbit;
$Eccent_Earth_Orbit = '0.016708634' - $Eccent_Earth_Orbit;

$Sun_Eq_of_Ctr_1 = deg2rad(3 * $Geom_Mean_Anom_Sun_deg);
$Sun_Eq_of_Ctr_1 = sin($Sun_Eq_of_Ctr_1);
$Sun_Eq_of_Ctr_1 = $Sun_Eq_of_Ctr_1 * '0.000289';
$Sun_Eq_of_Ctr_2 = '0.000101' * $Julian_Century;
$Sun_Eq_of_Ctr_2 = '0.019993' - $Sun_Eq_of_Ctr_2;
$Sun_Eq_of_Ctr_3 = deg2rad(2* $Geom_Mean_Anom_Sun_deg);
$Sun_Eq_of_Ctr_3 = sin($Sun_Eq_of_Ctr_3);
$Sun_Eq_of_Ctr = $Sun_Eq_of_Ctr_2 * $Sun_Eq_of_Ctr_3;
$Sun_Eq_of_Ctr = $Sun_Eq_of_Ctr + $Sun_Eq_of_Ctr_1;
$Sun_Eq_of_Ctr_3 = '0.000014' * $Julian_Century;
$Sun_Eq_of_Ctr_3 = $Sun_Eq_of_Ctr_3 + '0.004817';
$Sun_Eq_of_Ctr_3 = $Julian_Century * $Sun_Eq_of_Ctr_3;
$Sun_Eq_of_Ctr_3 = '1.914602' - $Sun_Eq_of_Ctr_3;
$Sun_Eq_of_Ctr_2 = deg2rad( $Geom_Mean_Anom_Sun_deg);
$Sun_Eq_of_Ctr_2 = sin($Sun_Eq_of_Ctr_2);
$Sun_Eq_of_Ctr_2 = $Sun_Eq_of_Ctr_2*$Sun_Eq_of_Ctr_3;
$Sun_Eq_of_Ctr = $Sun_Eq_of_Ctr_2 + $Sun_Eq_of_Ctr;

$Sun_True_Long_deg = $Geom_Mean_Long_Sun_deg + $Sun_Eq_of_Ctr;

$Sun_True_Anom_deg = $Geom_Mean_Anom_Sun_deg + $Sun_Eq_of_Ctr;

$Sun_Rad_Vector_AUs_1 = deg2rad($Sun_True_Anom_deg);
$Sun_Rad_Vector_AUs_1 = cos($Sun_Rad_Vector_AUs_1);
$Sun_Rad_Vector_AUs_1 = $Eccent_Earth_Orbit * $Sun_Rad_Vector_AUs_1;
$Sun_Rad_Vector_AUs = 1 + $Sun_Rad_Vector_AUs_1;
$Sun_Rad_Vector_AUs_1  = $Eccent_Earth_Orbit*$Eccent_Earth_Orbit;
$Sun_Rad_Vector_AUs_1 = 1-$Sun_Rad_Vector_AUs_1;
$Sun_Rad_Vector_AUs_1 = '1.000001018'*$Sun_Rad_Vector_AUs_1;
$Sun_Rad_Vector_AUs = $Sun_Rad_Vector_AUs_1 / $Sun_Rad_Vector_AUs;

$Sun_App_Long_deg_1 = '1934.136'*$Julian_Century;
$Sun_App_Long_deg_1 = '125.04'-$Sun_App_Long_deg_1;
$Sun_App_Long_deg_1 = deg2rad($Sun_App_Long_deg_1);
$Sun_App_Long_deg_1 = sin($Sun_App_Long_deg_1);
$Sun_App_Long_deg_1 = '0.00478'*$Sun_App_Long_deg_1;
$Sun_App_Long_deg = $Sun_True_Long_deg - '0.00569'-$Sun_App_Long_deg_1;

$Mean_Obliq_Ecliptic_deg = $Julian_Century * '0.001813';
$Mean_Obliq_Ecliptic_deg = '0.00059'- $Mean_Obliq_Ecliptic_deg;
$Mean_Obliq_Ecliptic_deg = $Julian_Century * $Mean_Obliq_Ecliptic_deg;
$Mean_Obliq_Ecliptic_deg = '46.815' + $Mean_Obliq_Ecliptic_deg;
$Mean_Obliq_Ecliptic_deg = $Julian_Century * $Mean_Obliq_Ecliptic_deg;
$Mean_Obliq_Ecliptic_deg = '21.448' - $Mean_Obliq_Ecliptic_deg;
$Mean_Obliq_Ecliptic_deg = $Mean_Obliq_Ecliptic_deg / 60;
$Mean_Obliq_Ecliptic_deg = '26' + $Mean_Obliq_Ecliptic_deg;
$Mean_Obliq_Ecliptic_deg = $Mean_Obliq_Ecliptic_deg / 60;
$Mean_Obliq_Ecliptic_deg = 23 + $Mean_Obliq_Ecliptic_deg;

$Obliq_Corr_deg = '1934.136'*$Julian_Century;
$Obliq_Corr_deg = '125.04'-$Obliq_Corr_deg;
$Obliq_Corr_deg = deg2rad($Obliq_Corr_deg);
$Obliq_Corr_deg = cos($Obliq_Corr_deg);
$Obliq_Corr_deg = $Obliq_Corr_deg*'0.00256';
$Obliq_Corr_deg = $Mean_Obliq_Ecliptic_deg + $Obliq_Corr_deg;

$Sun_Declin_deg_1 = deg2rad($Sun_App_Long_deg);
$Sun_Declin_deg_1 = sin($Sun_Declin_deg_1);
$Sun_Declin_deg_2 = deg2rad($Obliq_Corr_deg);
$Sun_Declin_deg_2 = sin($Sun_Declin_deg_2);
$Sun_Declin_deg = asin($Sun_Declin_deg_1*$Sun_Declin_deg_2);
$Sun_Declin_deg = rad2deg($Sun_Declin_deg);

$SunRt_Ascen_deg_1 = deg2rad($Sun_App_Long_deg);
$SunRt_Ascen_deg_1 = sin($SunRt_Ascen_deg_1);
$SunRt_Ascen_deg_2 = deg2rad($Obliq_Corr_deg);
$SunRt_Ascen_deg_2 = cos($SunRt_Ascen_deg_2);
$SunRt_Ascen_deg_1 = $SunRt_Ascen_deg_2 * $SunRt_Ascen_deg_1;
$SunRt_Ascen_deg_2 = deg2rad($Sun_App_Long_deg);
$SunRt_Ascen_deg_2 = cos($SunRt_Ascen_deg_2);
$SunRt_Ascen_deg = atan2($SunRt_Ascen_deg_1, $SunRt_Ascen_deg_2);
$SunRt_Ascen_deg = rad2deg($SunRt_Ascen_deg);

$y_1 = $Obliq_Corr_deg/2;
$y_1 = deg2rad($y_1);
$y_1 = tan($y_1);
$y = $Obliq_Corr_deg/2;
$y = deg2rad($y);
$y = tan($y);
$y = $y * $y_1;

$Eq_of_Time_minutes_1 = deg2rad($Geom_Mean_Anom_Sun_deg);
$Eq_of_Time_minutes_1 = 2 * $Eq_of_Time_minutes_1;
$Eq_of_Time_minutes_1 = sin($Eq_of_Time_minutes_1);
$Eq_of_Time_minutes_1 = '1.25' * $Eccent_Earth_Orbit * $Eccent_Earth_Orbit * $Eq_of_Time_minutes_1;
$Eq_of_Time_minutes_2 = deg2rad($Geom_Mean_Long_Sun_deg);
$Eq_of_Time_minutes_2 = 4 * $Eq_of_Time_minutes_2;
$Eq_of_Time_minutes_2 = sin($Eq_of_Time_minutes_2);
$Eq_of_Time_minutes_2 = '0.5' * $y * $y * $Eq_of_Time_minutes_2;
$Eq_of_Time_minutes_3 = deg2rad($Geom_Mean_Long_Sun_deg);
$Eq_of_Time_minutes_3 = 2 * $Eq_of_Time_minutes_3;
$Eq_of_Time_minutes_3 = cos($Eq_of_Time_minutes_3);
$Eq_of_Time_minutes_4 = deg2rad($Geom_Mean_Anom_Sun_deg);
$Eq_of_Time_minutes_4 = sin($Eq_of_Time_minutes_4);
$Eq_of_Time_minutes_4 = 4 * $Eccent_Earth_Orbit * $y * $Eq_of_Time_minutes_4 * $Eq_of_Time_minutes_3;
$Eq_of_Time_minutes_5 = deg2rad($Geom_Mean_Anom_Sun_deg);
$Eq_of_Time_minutes_5 = sin($Eq_of_Time_minutes_5);
$Eq_of_Time_minutes_5 = 2 * $Eccent_Earth_Orbit * $Eq_of_Time_minutes_5;
$Eq_of_Time_minutes_6 = deg2rad($Geom_Mean_Long_Sun_deg);
$Eq_of_Time_minutes_6 = 2 * $Eq_of_Time_minutes_6;
$Eq_of_Time_minutes_6 = sin($Eq_of_Time_minutes_6);
$Eq_of_Time_minutes_6 = $y * $Eq_of_Time_minutes_6;

$Eq_of_Time_minutes = $Eq_of_Time_minutes_6 - $Eq_of_Time_minutes_5 + $Eq_of_Time_minutes_4 - $Eq_of_Time_minutes_2 - $Eq_of_Time_minutes_1;
$Eq_of_Time_minutes = 4 * rad2deg($Eq_of_Time_minutes);

$HA_Sunrise_deg_1 = deg2rad($Sun_Declin_deg);
$HA_Sunrise_deg_1 = tan($HA_Sunrise_deg_1);
$HA_Sunrise_deg_2 = deg2rad($ma_latitude);
$HA_Sunrise_deg_2 = tan($HA_Sunrise_deg_2);
$HA_Sunrise_deg_1 = $HA_Sunrise_deg_1 * $HA_Sunrise_deg_2;
$HA_Sunrise_deg_2 = deg2rad($Sun_Declin_deg);
$HA_Sunrise_deg_5 = cos($HA_Sunrise_deg_2);
$HA_Sunrise_deg_3 = deg2rad($ma_latitude);
$HA_Sunrise_deg_3 = cos($HA_Sunrise_deg_3);
$HA_Sunrise_deg_3 = $HA_Sunrise_deg_3 * $HA_Sunrise_deg_5;
$HA_Sunrise_deg_4 = deg2rad('90.833');
$HA_Sunrise_deg_4 = cos($HA_Sunrise_deg_4);
$HA_Sunrise_deg = $HA_Sunrise_deg_4 / $HA_Sunrise_deg_3;
$HA_Sunrise_deg = $HA_Sunrise_deg - $HA_Sunrise_deg_1;
$HA_Sunrise_deg = acos($HA_Sunrise_deg);
$HA_Sunrise_deg = rad2deg($HA_Sunrise_deg);

$Solar_Noon_LST = $time_zone * 60;
$Solar_Noon_LST_1 = 4 * $ma_longitude;
$Solar_Noon_LST = 720 - $Solar_Noon_LST_1 - $Eq_of_Time_minutes + $Solar_Noon_LST;
$Solar_Noon_Hours = floor($Solar_Noon_LST / 60);
$Solar_Noon_Minutes = $Solar_Noon_LST % 60;
$Solar_Noon_LST = $Solar_Noon_LST / 1440;

$Sunrise_Time_LST = $HA_Sunrise_deg*4;
$Sunrise_Time_LST = $Sunrise_Time_LST / 1440;
$Sunrise_Time_LST = $Solar_Noon_LST - $Sunrise_Time_LST;
$Sunrise_Time_Hours = floor($Sunrise_Time_LST * 1440 / 60);
$Sunrise_Time_Minutes = ($Sunrise_Time_LST * 1440) % 60;

$Sunset_Time_LST = $HA_Sunrise_deg*4;
$Sunset_Time_LST = $Sunset_Time_LST / 1440;
$Sunset_Time_LST = $Solar_Noon_LST + $Sunset_Time_LST;
$Sunset_Time_Hours = floor($Sunset_Time_LST * 1440 / 60);
$Sunset_Time_Minutes = ($Sunset_Time_LST * 1440) % 60;

$Sunlight_Duration_minutes = 8* $HA_Sunrise_deg;
 
$True_Solar_Time_min_1 = 60 * $time_zone;
$True_Solar_Time_min_2 = 4 * $ma_longitude;
$True_Solar_Time_min_3 = $Time_past_local_midnight * 1440;
$True_Solar_Time_min = $True_Solar_Time_min_3 + $Eq_of_Time_minutes + $True_Solar_Time_min_2 - $True_Solar_Time_min_1;
$True_Solar_Time_min = fmod($True_Solar_Time_min, 1440);
$True_Solar_Time_Hours = floor($True_Solar_Time_min / 60);
$True_Solar_Time_Minutes = $True_Solar_Time_min % 60;

if ($True_Solar_Time_min/4 < 0)
{
   $Hour_Angle_deg = $True_Solar_Time_min / 4;
   $Hour_Angle_deg = $Hour_Angle_deg +180;
}
else
{
   $Hour_Angle_deg = $True_Solar_Time_min / 4;
   $Hour_Angle_deg = $Hour_Angle_deg -180;
}

$Solar_Zenith_Angle_deg_1 = deg2rad($Hour_Angle_deg);
$Solar_Zenith_Angle_deg_1  = cos($Solar_Zenith_Angle_deg_1 );
$Solar_Zenith_Angle_deg_2 = deg2rad($Sun_Declin_deg);
$Solar_Zenith_Angle_deg_2  = cos($Solar_Zenith_Angle_deg_2 );
$Solar_Zenith_Angle_deg_3 = deg2rad($ma_latitude);
$Solar_Zenith_Angle_deg_3  = cos($Solar_Zenith_Angle_deg_3 );
$Solar_Zenith_Angle_deg_3 = $Solar_Zenith_Angle_deg_1 * $Solar_Zenith_Angle_deg_2 * $Solar_Zenith_Angle_deg_3;
$Solar_Zenith_Angle_deg_4 = deg2rad($Sun_Declin_deg);
$Solar_Zenith_Angle_deg_4 = sin($Solar_Zenith_Angle_deg_4);
$Solar_Zenith_Angle_deg_5 = deg2rad($ma_latitude);
$Solar_Zenith_Angle_deg_5 = sin($Solar_Zenith_Angle_deg_5);
$Solar_Zenith_Angle_deg_5 = $Solar_Zenith_Angle_deg_5  * $Solar_Zenith_Angle_deg_4;   
$Solar_Zenith_Angle_deg = $Solar_Zenith_Angle_deg_5 + $Solar_Zenith_Angle_deg_3;
$Solar_Zenith_Angle_deg = acos($Solar_Zenith_Angle_deg);
$Solar_Zenith_Angle_deg = rad2deg($Solar_Zenith_Angle_deg);

$Solar_Elevation_Angle_deg = 90 - $Solar_Zenith_Angle_deg;

if ($Solar_Elevation_Angle_deg > 85)
{
    $Approx_Atmospheric_Refraction_deg = 0;
}
else
    if ($Solar_Elevation_Angle_deg > 5)
    {
        $Approx_Atmospheric_Refraction_deg = 58.1 / tan(deg2rad($Solar_Elevation_Angle_deg)) -0.07 / pow(tan(deg2rad($Solar_Elevation_Angle_deg)),3) + 0.000086/pow(tan(deg2rad($Solar_Elevation_Angle_deg)),5);
    }
    else
        if ($Solar_Elevation_Angle_deg > -0.575)
        {
            $Approx_Atmospheric_Refraction_deg_1 = -12.79 + $Solar_Elevation_Angle_deg * 0.711;
            $Approx_Atmospheric_Refraction_deg_2 = 103.4 + $Solar_Elevation_Angle_deg * $Approx_Atmospheric_Refraction_deg_1;
            $Approx_Atmospheric_Refraction_deg_3 = -518.2 + $Solar_Elevation_Angle_deg * $Approx_Atmospheric_Refraction_deg_2;
            $Approx_Atmospheric_Refraction_deg = 1735 + $Solar_Elevation_Angle_deg * $Approx_Atmospheric_Refraction_deg_3;
        }
        else
        {
            $Approx_Atmospheric_Refraction_deg = -20.772 / tan(deg2rad($Solar_Elevation_Angle_deg));
        }
$Approx_Atmospheric_Refraction_deg = $Approx_Atmospheric_Refraction_deg / 3600;
$Solar_Elevation_corrected_for_atm_refraction_deg = $Solar_Elevation_Angle_deg + $Approx_Atmospheric_Refraction_deg;

$Solar_Azimuth_Angle_deg_cw_from_N_1 = deg2rad($Solar_Zenith_Angle_deg);
$Solar_Azimuth_Angle_deg_cw_from_N_1 = sin($Solar_Azimuth_Angle_deg_cw_from_N_1);
$Solar_Azimuth_Angle_deg_cw_from_N_2 = deg2rad($ma_latitude);
$Solar_Azimuth_Angle_deg_cw_from_N_2 = cos($Solar_Azimuth_Angle_deg_cw_from_N_2 );
$Solar_Azimuth_Angle_deg_cw_from_N_1 = $Solar_Azimuth_Angle_deg_cw_from_N_1 * $Solar_Azimuth_Angle_deg_cw_from_N_2;
$Solar_Azimuth_Angle_deg_cw_from_N_3 = deg2rad($Sun_Declin_deg);
$Solar_Azimuth_Angle_deg_cw_from_N_3 = sin($Solar_Azimuth_Angle_deg_cw_from_N_3);
$Solar_Azimuth_Angle_deg_cw_from_N_4 = deg2rad($Solar_Zenith_Angle_deg);
$Solar_Azimuth_Angle_deg_cw_from_N_4 = cos($Solar_Azimuth_Angle_deg_cw_from_N_4);
$Solar_Azimuth_Angle_deg_cw_from_N_5 = deg2rad($ma_latitude);   
$Solar_Azimuth_Angle_deg_cw_from_N_5 = sin($Solar_Azimuth_Angle_deg_cw_from_N_5 );
$Solar_Azimuth_Angle_deg_cw_from_N_5 = $Solar_Azimuth_Angle_deg_cw_from_N_5 * $Solar_Azimuth_Angle_deg_cw_from_N_4;
$Solar_Azimuth_Angle_deg_cw_from_N_5 = $Solar_Azimuth_Angle_deg_cw_from_N_5 - $Solar_Azimuth_Angle_deg_cw_from_N_3;   
$Solar_Azimuth_Angle_deg_cw_from_N = acos($Solar_Azimuth_Angle_deg_cw_from_N_5 / $Solar_Azimuth_Angle_deg_cw_from_N_1);   

if ($Hour_Angle_deg > 0)
{
   $Solar_Azimuth_Angle_deg_cw_from_N = rad2deg($Solar_Azimuth_Angle_deg_cw_from_N);
   $Solar_Azimuth_Angle_deg_cw_from_N = $Solar_Azimuth_Angle_deg_cw_from_N + 180;
   $Solar_Azimuth_Angle_deg_cw_from_N = fmod($Solar_Azimuth_Angle_deg_cw_from_N,360);
}
else
{
   $Solar_Azimuth_Angle_deg_cw_from_N = rad2deg($Solar_Azimuth_Angle_deg_cw_from_N);
   $Solar_Azimuth_Angle_deg_cw_from_N = 540 - $Solar_Azimuth_Angle_deg_cw_from_N;
   $Solar_Azimuth_Angle_deg_cw_from_N = fmod($Solar_Azimuth_Angle_deg_cw_from_N,360);
}

$Solar_Azimuth_Angle_deg = round($Solar_Azimuth_Angle_deg_cw_from_N);

// AssociÃ© le point cardinal Ã  l'angle
if ($Solar_Azimuth_Angle_deg < '11.25' )
   { $Solar_Cardinal_point = 1;}      // NORD
   
if ($Solar_Azimuth_Angle_deg >= '11.25')
   { $Solar_Cardinal_point = 2;}      // NORD-NORD-EST
   
 if ($Solar_Azimuth_Angle_deg >= '33.75')
   {$Solar_Cardinal_point = 3;}      //NORD-EST
   
 if ($Solar_Azimuth_Angle_deg >= '56.25')
   {$Solar_Cardinal_point = 4;}      //EST-NORD-EST
   
 if ($Solar_Azimuth_Angle_deg >= '67.5')
   {$Solar_Cardinal_point = 5;}      //EST
   
 if ($Solar_Azimuth_Angle_deg >= '101.25')
   {$Solar_Cardinal_point = 6;}      //EST-SUD-EST
   
 if ($Solar_Azimuth_Angle_deg >= '123.75')
   {$Solar_Cardinal_point = 7;}      //SUD-EST
   
 if ($Solar_Azimuth_Angle_deg >= '146.25')
   {$Solar_Cardinal_point = 8;}      //SUD-SUD-EST
   
 if ($Solar_Azimuth_Angle_deg >= '168.75')
   {$Solar_Cardinal_point = 9;}      //SUD
   
 if ($Solar_Azimuth_Angle_deg >= '191.25')
   {$Solar_Cardinal_point = 10;}      //SUD-SUD-OUEST
   
 if ($Solar_Azimuth_Angle_deg >= '213.75')
   {$Solar_Cardinal_point = 11;}      //SUD-OUEST
   
 if ($Solar_Azimuth_Angle_deg >= '236.25')
   {$Solar_Cardinal_point = 12;}      //OUEST-SUD-OUEST
   
 if ($Solar_Azimuth_Angle_deg >= '258.75')
   {$Solar_Cardinal_point = 13;}      //OUEST
   
 if ($Solar_Azimuth_Angle_deg >= '281.25')
   {$Solar_Cardinal_point = 14;}      // OUEST-NORD-OUEST
   
 if ($Solar_Azimuth_Angle_deg >= '303.75')
   {$Solar_Cardinal_point = 15;}      // NORD-OUEST
   
 if ($Solar_Azimuth_Angle_deg >= '326.25')
   {$Solar_Cardinal_point = 16;}      //NORD-NORD-OUEST

   
$content_type = 'text/xml';
sdk_header($content_type);

echo "<Data>";
echo "<Parametres>";
echo "<Latitude>".$ma_latitude."</Latitude>";
echo "<Longitude>".$ma_longitude."</Longitude>";
echo "<Time_past_local_midnight>".$Time_past_local_midnight."</Time_past_local_midnight>";
echo "</Parametres>";
echo "<Soleil>";
printf("<True_Solar_Time>%02dH%02d</True_Solar_Time>", $True_Solar_Time_Hours, $True_Solar_Time_Minutes);
printf("<Sunrise_Time>%02dH%02d</Sunrise_Time>", $Sunrise_Time_Hours, $Sunrise_Time_Minutes);
printf("<Solar_Noon>%02dH%02d</Solar_Noon>", $Solar_Noon_Hours, $Solar_Noon_Minutes);
printf("<Sunset_Time>%02dH%02d</Sunset_Time>", $Sunset_Time_Hours, $Sunset_Time_Minutes);
echo "<Sunlight_Duration_minutes_round>".round($Sunlight_Duration_minutes)."</Sunlight_Duration_minutes_round>";
echo "<Solar_Elevation>".$Solar_Elevation_Angle_deg."</Solar_Elevation>";
echo "<Solar_Elevation_corrected_for_atm_refraction>".$Solar_Elevation_corrected_for_atm_refraction_deg."</Solar_Elevation_corrected_for_atm_refraction>";
echo "<Solar_Azimuth>".$Solar_Azimuth_Angle_deg_cw_from_N."</Solar_Azimuth>";
echo "<Solar_Azimuth_round>".$Solar_Azimuth_Angle_deg."</Solar_Azimuth_round>";
echo "<Solar_Cardinal_point>".$Solar_Cardinal_point."</Solar_Cardinal_point>";
echo "</Soleil>";
echo "</Data>";

?>

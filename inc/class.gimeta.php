<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of efiMetadatas, a plugin for Dotclear 2.
# 
# Copyright (c) 2009-2010 JC Denis and contributors
# jcdenis@gdwd.com
# 
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
# -- END LICENSE BLOCK ------------------------------------

class giMeta
{
	
	public static function imgMeta($core,$src)
	{
		$metas = array(
			'Title' => array(__('Title'),''),
			'Description' => array(__('Description'),''),
			'Location' => array(__('Location'),''),
			'DateTimeOriginal' => array(__('Date'),''),
			'Make' => array(__('Manufacturer'),''),
			'Model' => array(__('Model'),''),
			'Lens' => array(__('Lens'),''),
			'ExposureProgram' => array(__('Program'),''),
			'Exposure' => array(__('Speed'),''),
			'FNumber' => array(__('Aperture'),''),
			'ISOSpeedRatings' => array(__('ISO'),''),
			'FocalLength' => array(__('Focal'),''),
			'ExposureBiasValue' => array(__('Exposure Bias'),''),
			'MeteringMode' => array(__('Metering mode'),'')
		);
		
		$exp_prog = array(
			0 => __('Not defined'),
			1 => __('Manual'),
			2 => __('Normal program'),
			3 => __('Aperture priority'),
			4 => __('Shutter priority'),
			5 => __('Creative program'),
			6 => __('Action program'),
			7 => __('Portait mode'),
			8 => __('Landscape mode')
		);
		
		$met_mod = array(
			0 => __('Unknow'),
			1 => __('Average'),
			2 => __('Center-weighted average'),
			3 => __('Spot'),
			4 => __('Multi spot'),
			5 => __('Pattern'),
			6 => __('Partial'),
			7 => __('Other')
		);
		
		if (!$src || !file_exists($src)) return false;
		
		$m = imageMeta::readMeta($src);
		
		//echo '<HR>';
		//foreach ($m as $k => $v)
		//	echo $k . '=' . $v . '<BR>';
		//echo '<HR>';
		
		if (empty($m['Make'])) return false;
		
		# Title
		if (!empty($m['Title']))
		{
			$metas['Title'][1] = html::escapeHTML($m['Title']);
		}
		
		# Description
		if (!empty($m['Description']))
		{
			if (!empty($m['Title']) && $m['Title'] != $m['Description'])
			{
				$metas['Description'][1] = html::escapeHTML($m['Description']);
			}
		}
		
		# Location
		if (!empty($m['City']))
		{
			$metas['Location'][1] .= html::escapeHTML($m['City']);
		}
		if (!empty($m['City']) && !empty($m['country']))
		{
			$metas['Location'][1] .= ', ';
		}
		if (!empty($m['country']))
		{
			$metas['Location'][1] .= html::escapeHTML($m['Country']);
		}
		
		# DateTimeOriginal
		if (!empty($m['DateTimeOriginal']))
		{
			$dt_ft = $core->blog->settings->system->date_format.', '.$core->blog->settings->system->time_format;
			$dt_tz = $core->blog->settings->system->blog_timezone;
			$metas['DateTimeOriginal'][1] = dt::dt2str($dt_ft,$m['DateTimeOriginal'],$dt_tz);
		}
		
		# Make
		if (isset($m['Make']))
		{
			$metas['Make'][1] = html::escapeHTML($m['Make']);
		}
		
		# Model
		if (isset($m['Model']))
		{
			$metas['Model'][1] = html::escapeHTML($m['Model']);
		}
		
		# Lens
		if (isset($m['Lens']))
		{
			$metas['Lens'][1] = html::escapeHTML($m['Lens']);
		}
		
		# ExposureProgram
		if (isset($m['ExposureProgram']))
		{
			$metas['ExposureProgram'][1] = isset($exp_prog[$m['ExposureProgram']]) ?
			$exp_prog[$m['ExposureProgram']] : $m['ExposureProgram'];
		}
		
		# Exposure
		if (!empty($m['Exposure']))
		{
			@eval('$s = ' . $m['Exposure'] . ';');
			if ($s >= 0.5) {
				$metas['Exposure'][1] = $s.'s';
			} else {
				$metas['Exposure'][1] = $m['Exposure'].'s';
			}
			//$metas['Exposure'][1] = $m['Exposure'].'s';
		}
		
		# FNumber
		if (!empty($m['FNumber']))
		{
			$ap = sscanf($m['FNumber'],'%d/%d');
			@$metas['FNumber'][1] = $ap ? 'f/'.( $ap[0] / $ap[1]) :	$m['FNumber'];
		}
		
		# ISOSpeedRatings
		if (!empty($m['ISOSpeedRatings']))
		{
			if (is_array($m['ISOSpeedRatings'])) {
				$metas['ISOSpeedRatings'][1] = $m['ISOSpeedRatings'][1];
			} else {
				$metas['ISOSpeedRatings'][1] = $m['ISOSpeedRatings'];
			}
		}
		
		# FocalLength
		if (!empty($m['FocalLength']))
		{
			$fl = sscanf($m['FocalLength'],'%d/%d');
			@$metas['FocalLength'][1] = $fl ? $fl[0]/$fl[1].'mm' : $m['FocalLength'];
		}
		
		# ExposureBiasValue
		if (isset($m['ExposureBiasValue']))
		{
			$metas['ExposureBiasValue'][1] = $m['ExposureBiasValue'];
		}
		
		# MeteringMode
		if (isset($m['MeteringMode']))
		{
			$metas['MeteringMode'][1] = isset($met_mod[$m['MeteringMode']]) ?
			$exp_prog[$m['MeteringMode']] : $m['MeteringMode'];
		}
		
		return $metas;
	}
}
?>
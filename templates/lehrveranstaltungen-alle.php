<?php 

namespace RRZE\UnivIS;

$aAllowedColors = [
    'med',
    'nat',
    'rw',
    'phil',
    'tk',
];

$this->atts['color'] = implode('', array_intersect($this->show, $aAllowedColors));
$this->atts['color_courses'] = explode('_', implode('', array_intersect($this->show, preg_filter('/$/', '_courses', $aAllowedColors))));
$this->atts['color_courses'] = $this->atts['color_courses'][0];

$ret = '<div class="rrze-univis">';
if ($data){
    $lang = get_locale();

    if (in_array('accordion', $this->show) || in_array('accordion_courses', $this->show)){
        $ret .= '[collapsibles hstart="' . $this->atts['hstart'] . '"]';
    }

    foreach ($data as $type => $lectures){
        if (in_array('accordion', $this->show)){
            $ret .= '[collapse title="' . $type . '" name="' . urlencode($type) . '" color="' . $this->atts['color'] . '"]';
        }else{
            $ret .= '<h' . $this->atts['hstart'] . '>' . $type . '</h' . $this->atts['hstart'] . '>';
        }

        $ret .= '<ul>';
        foreach ($lectures as $lecture){
            $courseDates = '';
            $url = get_permalink() . 'lv_id/' . $lecture['lecture_id'];
			$ret .= '<li>';
            $ret .= '<h' . ($this->atts['hstart'] + 1) . '><a href="' . $url . '">';
            if ($lang != 'de_DE' && $lang != 'de_DE_formal' && !empty($lecture['ects_name'])) {
                $lecture['title'] = $lecture['ects_name'];
            } else {
                $lecture['title'] = $lecture['name'];
            }
            $ret .= $lecture['title'];
            $ret .= '</a></h' . ($this->atts['hstart'] + 1) . '>';
            if (!empty($lecture['comment']) && !in_array('comment', $this->hide)) {
                $ret .= '<p>' . make_clickable($lecture['comment']) . '</p>';
            }
            if (!empty($lecture['organizational']) && !in_array('organizational', $this->hide)) {
                $ret .= '<p>' . make_clickable($lecture['organizational']) . '</p>';
            }

            $ret .= '<ul class="terminmeta">';
            $ret .= '<li>';
            $infos = '';
            if (!empty($lecture['sws'])) {
                $infos .= '<span>' . $lecture['sws'] . '</span>';
            }
            if (!empty($lecture['maxturnout'])) {
                if (!empty($infos)) {$infos .= '; ';}
                $infos .= '<span>' . __('Expected participants', 'rrze-univis') . ': ' . $lecture['maxturnout'] . '</span>';
            }
            if (!empty($lecture['fruehstud'])) {
                if (!empty($infos)) {$infos .= '; ';}
                $infos .= '<span>' . $lecture['fruehstud'] . '</span>';
            }
            if (!empty($lecture['gast'])) {
                if (!empty($infos)) {$infos .= '; ';}
                $infos .= '<span>' . $lecture['gast'] . '</span>';
            }
            if (!empty($lecture['schein'])) {
                if (!empty($infos)) {$infos .= '; ';}
                $infos .= '<span>' . $lecture['schein'] . '</span>';
            }
            if (!empty($lecture['ects'])) {
                if (!empty($infos)) {$infos .= '; ';}
                $infos .= '<span>' . $lecture['ects'] . '</span>';
                if (!empty($lecture['ects_cred'])) {
                    $infos .= ' (' . $lecture['ects_cred'] . ')';
                }
                $infos .= '</span>';
            }
            if (!empty($lecture['leclanguage_long']) && ($lecture['leclanguage_long'] != __('Lecture\'s language German', 'rrze-univis'))) {
                if (!empty($infos)) {$infos .= ', ';}
                $infos .= '<span>' . $lecture['leclanguage_long'] . '</span>';
            }
            $ret .= $infos . '</li>';

            if (in_array('accordion_courses', $this->show)){
                if (in_array('accordion', $this->show)){
                    if (empty($courseDates)){
                        $courseDates = '[accordion hstart="' . ($this->atts['hstart'] + 1) . '"]';
                    }
                    $courseDates .= '[accordion-item title="' . __('Date', 'rrze-univis') . '" name="' . __('Date', 'rrze-univis') . '_' . urlencode($lecture['title']) . '" color="' . $this->atts['color_courses'] . '"]';
                }else{
                    $courseDates = '[collapse title="' . __('Date', 'rrze-univis') . '" name="' . __('Date', 'rrze-univis') . '_' . urlencode($lecture['title']) . '" color="' . $this->atts['color_courses'] . '"]';
                }
            }else{
                $courseDates = '<li class="termindaten">' . __('Date', 'rrze-univis') . ':';
            }
            $courseDates .= '<ul>';

            if (isset($lecture['courses'])){
                foreach ($lecture['courses'] as $course){
                    if ((empty($lecture['lecturer_key']) || empty($course['doz'])) || (!empty($lecture['lecturer_key']) && !empty($course['doz']) && (in_array($lecture['lecturer_key'], $course['doz'])))) {
                        foreach ($course['term'] as $term){
                            $t = array();
                            $time = array();
                            if (!empty($term['repeat'])){
                                $t['repeat'] = $term['repeat'];
                            }
                            if (!empty($term['startdate'])){
                                if (!empty($term['enddate']) && $term['startdate'] != $term['enddate']){
                                    $t['date'] = date("d.m.Y", strtotime($term['startdate'])) . '-' . date("d.m.Y", strtotime($term['enddate']));
                                }else{
                                    $t['date'] = date("d.m.Y", strtotime($term['startdate']));
                                }
                            }
                            if (!empty($term['starttime'])){
                                $time['starttime'] = $term['starttime'];
                            }
                            if (!empty($term['endtime'])){
                                $time['endtime'] = $term['endtime'];
                            }
                            if (!empty($time)){
                                $t['time'] = $time['starttime'] . '-' . $time['endtime'];
                            }else{
                                $t['time'] = __('Time on appointment', 'rrze-univis');
                            }
                            if (!empty($term['room']['short'])){
                                $t['room'] = __('Room', 'rrze-univis') . ' ' . $term['room']['short'];
                            }
                            if (!empty($term['exclude'])){
                                $t['exclude'] = '(' . __('exclude', 'rrze-univis') . ' ' . $term['exclude'] . ')';
                            }
                            if (!empty($course['coursename'])){
                                $t['coursename'] = '(' . __('Course', 'rrze-univis') . ' ' . $course['coursename'] . ')';
                            }
                            // ICS
                            // if (in_array('ics', $this->show) && !in_array('ics', $this->hide)) {
                            //     $aIcsLink = Functions::makeLinkToICS($type, $lecture, $term, $t);
                            //     $t['ics'] = '<span class="lecture-info-ics" itemprop="ics"><a href="' . $aIcsLink['link'] . '" aria-label="' . $aIcsLink['linkTxt'] . '">' . __('ICS', 'rrze-univis') . '</a></span>';
                            // }
                            $t['time'] .= ',';
                            $term_formatted = implode(' ', $t);
                            $courseDates .= '<li>' . $term_formatted . '</li>';
                        }
                    }
                }
                if (in_array('accordion_courses', $this->show)){
                    if (in_array('accordion', $this->show)){
                        $courseDates .= '[/accordion-item]';
                        $courseDates .= '[/accordion]';
                    }else{
                        $courseDates .= '[/collapse]';
                    }
                }
            }else{
                $courseDates .= '<li>' . __('Time and place on appointment', 'rrze-univis') . '</li>';
                if (in_array('accordion_courses', $this->show)){
                    if (in_array('accordion', $this->show)){
                        $courseDates .= '[/accordion-item]';
                        $courseDates .= '[/accordion]';
                    }else{
                        $courseDates .= '[/collapse]';
                    }
                }
            }
		    $courseDates .= '</ul>';
		    $courseDates .= '</li>';
		    $ret .= $courseDates. '</li>';
        }
        $ret .= '</ul>';

        if (in_array('accordion', $this->show)){
            $ret .= '[/collapse]';
        }
    }

    if (in_array('accordion', $this->show) || in_array('accordion_courses', $this->show)){
        $ret .= '[/collapsibles]';
        $ret = do_shortcode($ret);
    }
}

$ret .= '</div>';

echo $ret;
<?php

function icalender()
{
header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=calendar.ics');

// Define the basic structure of the iCalendar file
echo "BEGIN:VCALENDAR\n";
echo "VERSION:2.0\n";
echo "PRODID:-//hacksw/handcal//NONSGML v1.0//EN\n";

// Define the event
echo "BEGIN:VEVENT\n";
echo "UID:unique-event-identifier\n";
echo "DTSTART:20221121T090000\n";
echo "DTEND:20221121T170000\n";
echo "SUMMARY:My Event\n";
echo "LOCATION:Online\n";
echo "END:VEVENT\n";
	
// Close the iCalendar file
echo "END:VCALENDAR\n";	
}
#!/bin/bash

# vetsi rozliseni mozna i pro obrazky, nebot uz dnes ukazujeme vetsi na obou webech

while read src; do

	echo "File name processed: ${src}"
	delka="$(ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 ${src})"
	usek=$(echo "$delka / 8" | bc)
	echo "Movie length in seconds is: $delka we will seek by: $usek seconds"

	rm -rf u/listt.txt
	touch u/listt.txt

	for x in 1 2 3 4 5 6 7 8
	do
		sec=$(echo "($usek * $x) - 2" | bc)
		echo "Using $sec second offset for a seek"
		ffmpeg -v error -ss "$sec" -i "${src}" -t 4 -c copy -y -qscale 0 "u/o${x}.mp4" < /dev/null
		ffmpeg -v error -ss 1 -i "u/o${x}.mp4" -t 2 -y -qscale 0 "u/o${x}.avi" < /dev/null
		echo "file 'o$x.avi'" >> u/listt.txt
	done

	ffmpeg -v error -f concat -i u/listt.txt -c copy -an -y -vf scale=360:240 -crf 20 -c:v libx264 -preset slow "${src}.thumb.mp4" < /dev/null
	echo "File ${src} is done"
	rm -rf ./u/*

done <files.txt

exit

# removed options for quality purposes
 -vf scale=640:360 -crf 20
 -crf 20

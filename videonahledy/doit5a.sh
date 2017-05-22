#!/bin/bash

#src="/Users/xrevelon/Downloads/videos/Brutální-Jahoda---Reflektory-(Official-music-video).mp4"
#src="/Users/xrevelon/Downloads/videos/Chess.com-Kaidanovs-Comprehensive-Repertoire--Facing-the-French-2012.mp4"
#src="/Users/xrevelon/Downloads/videos/[The-Crows-Ita-Fansub]-Super-Sentai-World.mp4"
#src="/Users/xrevelon/Downloads/videos/ale-film-je-moje-milenka-(dokument-2010--o-I.Bergmanovi)CZ-TIT-IRISA.avi"
#src="/Users/xrevelon/Downloads/videos/Sedm Statečných (2016) 720p BRRip CZ-dabing.avi"
#src="/Users/xrevelon/Downloads/videos/Red-2-CZ-dabing-(2013)-NOVINKA_xvid.avi"


while read src; do

	echo "File name: ${src}"
	delka="$(ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 ${src})"
	usek=$(echo "$delka / 8" | bc)
	echo "Movie length in seconds is: $delka a stridat to budeme po $usek"

	rm -rf u/listt.txt
	touch u/listt.txt

	for x in 1 2 3 4 5 6 7 8
	do
		sec=$(echo "($usek * $x) - 2" | bc)
		echo "Using $sec second for seek"
		#sleep 1
		ffmpeg -v error -ss "$sec" -i "${src}" -t 4 -c copy -y -qscale 0 "u/o${x}.mp4" < /dev/null
		#sleep 1
		ffmpeg -v error -ss 1 -i "u/o${x}.mp4" -t 2 -y -qscale 0 "u/o${x}.avi" < /dev/null
		echo "file 'o$x.avi'" >> u/listt.txt
		#sleep 1
	done

	ffmpeg -v error -f concat -i u/listt.txt -c copy -an -y -crf 20 -vf scale=320:240 -c:v libx264 -preset slow "${src}.thumb.mp4" < /dev/null
	echo "File ${src} done"
	rm -rf ./u/*
	#sleep 5

done <files.txt

exit

# removed options for quality purposes
 -vf scale=640:360 -crf 0
 -crf 20

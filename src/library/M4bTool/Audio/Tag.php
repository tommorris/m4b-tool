<?php

namespace M4bTool\Audio;


use ArrayAccess;
use M4bTool\Parser\EmbeddedCover;

/**
 * Class Tag
 * @package M4bTool\Audio
 *
 * mp4tags reference:
 *
 * -b, -tempo       NUM  Set the tempo (beats per minute)
 * -d, -disk        NUM  Set the disk number
 * -D, -disks       NUM  Set the number of disks
 * -E, -tool        STR  Set the software used for encoding
 * -G, -grouping    STR  Set the grouping name
 * -H, -hdvideo     NUM  Set the HD flag (1\0)
 * -i, -type        STR  Set the Media Type("Movie", "TV Show", "Music Video", ...)
 * -I, -contentid   NUM  Set the content ID
 * -j, -genreid     NUM  Set the genre ID
 * -l, -longdesc    NUM  Set the long description
 * -L, -lyrics      NUM  Set the lyrics
 * -M, -episode     NUM  Set the episode number
 * -n, -season      NUM  Set the season number
 * -N, -network     STR  Set the TV network
 * -o, -episodeid   STR  Set the TV episode ID
 * -p, -playlistid  NUM  Set the playlist ID
 * -P, -picture     PTH  Set the picture as a .png
 * -B, -podcast     NUM  Set the podcast flag.
 * -S  -show        STR  Set the TV show
 * -t, -track       NUM  Set the track number
 * -T, -tracks      NUM  Set the number of tracks
 * -x, -xid         STR  Set the globally-unique xid (vendor:scheme:id)
 * -y, -year        NUM  Set the release date
 * -z, -artistid    NUM  Set the artist ID
 * -Z, -composerid  NUM  Set the composer ID
 * -W, -cast        STR  Set the cast|actors tag (AppleTV)
 * -F, -director    STR  Set the director tag (AppleTV)
 * -J, -codirector  STR  Set the codirector tag (AppleTV)
 * -K, -producers   STR  Set the producers tag (AppleTV)
 * -O, -swriters    STR  Set the screen writers tag (AppleTV)
 * -Q, -copywarning STR  Add copy warning (AppleTV)
 * -U, -studio      STR  Add film studio (AppleTV)
 * -Y, -rating      STR  Add film ratings (AppleTV)
 * -V  -rannotation STR  Add rating annotation to the ratings, ie rated r for violence
 * -X  -crating     STR  Add content rating tag. "Inoffensive", "Clean", "Explicit"
 * -r, -remove      STR  Remove tags by code (e.g. "-r cs" removes the comment and song tags)
 */
class Tag implements ArrayAccess
{
    const MP4_STIK_AUDIOBOOK = 2;
    const TRANSIENT_PROPERTIES = [
        "chapters",
        "removeProperties",
        "series",
        "seriesPart"
    ];

    public $encoder;
    public $title;
    public $sortTitle; // -sortname on mp4tags (means sort chapter title in itunes)
    public $artist;
    public $sortArtist; // -sortartist on mp4tags (means sort author in itunes)
    public $genre;
    public $writer;
    public $album;
    public $sortAlbum; // -sortalbum on mp4tags (means sort title in itunes)
    public $disk;
    public $disks;
    public $albumArtist;
    public $year;
    public $track;
    public $tracks;
    public $cover;
    public $description;
    public $longDescription;
    public $comment;
    public $copyright;
    public $encodedBy;
    public $type = self::MP4_STIK_AUDIOBOOK;

    // MP3 Specific
    public $performer; // TPE3
    public $language; // TLAN
    public $publisher; // TPUB
    public $lyrics; // TSLT

    /** @var Chapter[] */
    public $chapters = [];

    // pseudo tags that are used to auto generate sort properties, if not present
    public $series;
    public $seriesPart;

    public $removeProperties = [];


    public function mergeMissing(Tag $tag)
    {
        foreach ($this as $propertyName => $propertyValue) {
            if ($this->$propertyName === null || $this->$propertyName === "" || $this->$propertyName === []) {
                $this->$propertyName = $tag->$propertyName;
            }
        }
    }

    public function mergeOverwrite(Tag $tag)
    {
        foreach ($this as $propertyName => $propertyValue) {
            if ($tag->$propertyName === null || $tag->$propertyName === "" || $tag->$propertyName === []) {
                continue;
            }
            $this->$propertyName = $tag->$propertyName;
        }
    }

    public function hasCoverFile()
    {
        return $this->cover && !($this->cover instanceof EmbeddedCover);
    }

    public function isTransientProperty($propertyName)
    {
        return in_array($propertyName, static::TRANSIENT_PROPERTIES, true);
    }

    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return property_exists($this, $offset);
    }

    /**
     * Offset to retrieve
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Offset to set
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        $this->$offset = null;
    }
}

<?php
namespace Rox\Traits;
/**
 * Description of StringUtil
 *
 * @author Marcelo Araújo based on Rafael Goulart original Class
 */
trait RegexUtil
{

    private static $ACCENT_STRINGS = 'ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËẼÌÍÎÏĨÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëẽìíîïĩðñòóôõöøùúûüýÿ';
    private static $NO_ACCENT_STRINGS = 'SOZsozYYuAAAAAAACEEEEEIIIIIDNOOOOOOUUUUYsaaaaaaaceeeeeiiiiionoooooouuuuyy';

    /**
     * 
     *
     * @param string $text
     * @return string
     */
    public function getRegex($text)
    {
        $from = str_split(utf8_decode(self::$ACCENT_STRINGS));
        $to = str_split(strtolower(self::$NO_ACCENT_STRINGS));
        $text = utf8_decode($text);
        $regex = array();
        foreach ($to as $key => $value) {
            if (isset($regex[$value])) {
                $regex[$value] .= $from[$key];
            } else {
                $regex[$value] = $value;
            }
        }
        foreach ($regex as $rg_key => $rg) {
            $text = preg_replace("/[$rg]/", "_{$rg_key}_", $text);
        }
        foreach ($regex as $rg_key => $rg) {
            $text = preg_replace("/_{$rg_key}_/", "[$rg]", $text);
        }
        return utf8_encode($text);
    }
}
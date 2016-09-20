<?php
namespace Commonhelp\Util\Http;

class URLUtil{
	
	/**
	 * Encodes the path of a url.
	 *
	 * slashes (/) are treated as path-separators.
	 *
	 * @param string $path
	 * @return string
	 */
	public static function encodePath($path) {
		return preg_replace_callback('/([^A-Za-z0-9_\-\.~\(\)\/:@])/', function($match) {
        return '%' . sprintf('%02x', ord($match[0]));
    }, $path);
	}
	
	/**
	 * Encodes a 1 segment of a path
	 *
	 * Slashes are considered part of the name, and are encoded as %2f
	 *
	 * @param string $pathSegment
	 * @return string
	 */
	public static function encodePathSegment($pathSegment) {
		 return preg_replace_callback('/([^A-Za-z0-9_\-\.~\(\):@])/', function($match) {
        return '%' . sprintf('%02x', ord($match[0]));
    }, $pathSegment);
	}
	
	/**
	 * Decodes a url-encoded path
	 *
	 * @param string $path
	 * @return string
	 */
	public static function decodePath($path) {
		return self::decodePathSegment($path);
	}
	
	/**
	 * Decodes a url-encoded path segment
	 *
	 * @param string $path
	 * @return string
	 */
	public static function decodePathSegment($path) {
		$path = rawurldecode($path);
    $encoding = mb_detect_encoding($path, ['UTF-8', 'ISO-8859-1']);
    switch ($encoding) {
        case 'ISO-8859-1' :
            $path = utf8_encode($path);
    }
    return $path;
	}
	
	/**
	 * Returns the 'dirname' and 'basename' for a path.
	 *
	 * The reason there is a custom function for this purpose, is because
	 * basename() is locale aware (behaviour changes if C locale or a UTF-8 locale
	 * is used) and we need a method that just operates on UTF-8 characters.
	 *
	 * In addition basename and dirname are platform aware, and will treat
	 * backslash (\) as a directory separator on windows.
	 *
	 * This method returns the 2 components as an array.
	 *
	 * If there is no dirname, it will return an empty string. Any / appearing at
	 * the end of the string is stripped off.
	 *
	 * @param string $path
	 * @return array
	 */
	public static function splitPath($path) {
		$matches = [];
    if(preg_match('/^(?:(?:(.*)(?:\/+))?([^\/]+))(?:\/?)$/u', $path, $matches)){
        return [$matches[1], $matches[2]];
    }
    return [null,null];
	}
	
	/**
	 * Resolves relative urls, like a browser would.
	 *
	 * @param string $basePath
	 * @param string $newPath
	 * @return string
	 */
	public static function resolve($basePath, $newPath) {
    $base = parse($basePath);
    $delta = parse($newPath);
 		$pick = function($part) use ($base, $delta) {
        if($delta[$part]){
        	return $delta[$part];
        }else if ($base[$part]){
         	return $base[$part];
        }
        return null;
    };
    // If the new path defines a scheme, it's absolute and we can just return
    // that.
    if($delta['scheme']){
        return build($delta);
    }
    $newParts = [];
    $newParts['scheme'] = $pick('scheme');
    $newParts['host']   = $pick('host');
    $newParts['port']   = $pick('port');
    $path = '';
    if($delta['path']){
        // If the path starts with a slash
        if($delta['path'][0] === '/'){
            $path = $delta['path'];
        }else{
            // Removing last component from base path.
            $path = $base['path'];
            if(strpos($path, '/') !== false){
                $path = substr($path, 0, strrpos($path, '/'));
            }
            $path .= '/' . $delta['path'];
        }
    }else{
        $path = $base['path'] ?: '/';
    }
    // Removing .. and .
    $pathParts = explode('/', $path);
    $newPathParts = [];
    foreach($pathParts as $pathPart){
        switch($pathPart){
            //case '' :
            case '.' :
                break;
            case '..' :
                array_pop($newPathParts);
                break;
            default :
                $newPathParts[] = $pathPart;
                break;
        }
    }
    $path = implode('/', $newPathParts);
    // If the source url ended with a /, we want to preserve that.
    $newParts['path'] = $path;
    if ($delta['query']) {
        $newParts['query'] = $delta['query'];
    } elseif (!empty($base['query']) && empty($delta['host']) && empty($delta['path'])) {
        // Keep the old query if host and path didn't change
        $newParts['query'] = $base['query'];
    }
    if ($delta['fragment']) {
        $newParts['fragment'] = $delta['fragment'];
    }
    return build($newParts);
	}
	
}
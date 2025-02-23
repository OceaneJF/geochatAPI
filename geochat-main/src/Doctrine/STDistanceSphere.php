<?php

namespace App\Doctrine;

use CrEOF\Spatial\ORM\Query\AST\Functions\AbstractSpatialDQLFunction;

/**
 * ST_Distance_Sphere DQL function
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class STDistanceSphere extends AbstractSpatialDQLFunction
{
    protected $platforms = array('mysql');

    protected $functionName = 'ST_Distance_Sphere';
    protected $minGeomExpr = 2;
    protected $maxGeomExpr = 2;
}

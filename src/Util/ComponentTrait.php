<?php
/**
 * This file is part of the League.url library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/thephpleague/url/
 * @version 4.0.0
 * @package League.url
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace League\Url\Util;

use InvalidArgumentException;
use League\Url\Interfaces\Component;

/**
 * A trait to validate stringable data
 *
 * @package League.url
 * @since 1.0.0
 */
trait ComponentTrait
{
    /**
     * validate a string
     *
     * @param  mixed $str
     *
     * @throws InvalidArgumentException if the submitted data can not be converted to string
     *
     * @return string
     */
    protected function validateString($str)
    {
        if (is_null($str) || is_scalar($str) || (is_object($str) && method_exists($str, '__toString'))) {
            return trim($str);
        }

        throw new InvalidArgumentException(sprintf(
            'Data passed must be a valid string; received "%s"',
            (is_object($str) ? get_class($str) : gettype($str))
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function sameValueAs(Component $component)
    {
        return $component->getUriComponent() == $this->getUriComponent();
    }

    /**
     * {@inheritdoc}
     */
    public function withValue($value)
    {
        return new static($value);
    }
}
<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri;

use JsonSerializable;
use League\Uri\Contracts\UriInterface;
use League\Uri\Exceptions\SyntaxError;
use Psr\Http\Message\UriInterface as Psr7UriInterface;
use Stringable;

/**
 * @phpstan-import-type InputComponentMap from UriString
 */
final class Http implements Stringable, Psr7UriInterface, JsonSerializable
{
    private function __construct(private readonly UriInterface $uri)
    {
        $this->validate($this->uri);
    }

    /**
     * Validate the submitted uri against PSR-7 UriInterface.
     *
     * @throws SyntaxError if the given URI does not follow PSR-7 UriInterface rules
     */
    private function validate(UriInterface $uri): void
    {
        if (null === $uri->getScheme() && '' === $uri->getHost()) {
            throw new SyntaxError('An URI without scheme can not contains a empty host string according to PSR-7: '.$uri);
        }

        $port = $uri->getPort();
        if (null !== $port && ($port < 0 || $port > 65535)) {
            throw new SyntaxError('The URI port is outside the established TCP and UDP port ranges: '.$uri);
        }
    }

    /**
     * Create a new instance from a string or a stringable object.
     */
    public static function new(Stringable|string $uri = ''): self
    {
        return match (true) {
            $uri instanceof UriInterface => new self($uri),
            default => new self(Uri::new($uri)),
        };
    }

    /**
     * Create a new instance from a hash of parse_url parts.
     *
     * @param InputComponentMap $components a hash representation of the URI similar
     *                                      to PHP parse_url function result
     */
    public static function fromComponents(array $components): self
    {
        return new self(Uri::fromComponents($components));
    }

    /**
     * Create a new instance from the environment.
     */
    public static function fromServer(array $server): self
    {
        return new self(Uri::fromServer($server));
    }

    /**
     * Create a new instance from a URI and a Base URI.
     *
     * The returned URI must be absolute.
     */
    public static function fromBaseUri(Stringable|String $uri, Stringable|String|null $baseUri = null): self
    {
        return new self(Uri::fromBaseUri($uri, $baseUri));
    }

    public static function fromTemplate(Stringable|string $template, iterable $variables = []): self
    {
        return new self(Uri::fromTemplate($template, $variables));
    }

    public function getScheme(): string
    {
        return (string) $this->uri->getScheme();
    }

    public function getAuthority(): string
    {
        return (string) $this->uri->getAuthority();
    }

    public function getUserInfo(): string
    {
        return (string) $this->uri->getUserInfo();
    }

    public function getHost(): string
    {
        return (string) $this->uri->getHost();
    }

    public function getPort(): ?int
    {
        return $this->uri->getPort();
    }

    public function getPath(): string
    {
        return $this->uri->getPath();
    }

    public function getQuery(): string
    {
        return (string) $this->uri->getQuery();
    }

    public function getFragment(): string
    {
        return (string) $this->uri->getFragment();
    }

    public function __toString(): string
    {
        return $this->uri->toString();
    }

    public function jsonSerialize(): string
    {
        return $this->uri->toString();
    }

    /**
     * Safely stringify input when possible for League UriInterface compatibility.
     */
    private function filterInput(string $str): string|null
    {
        return match (true) {
            '' === $str => null,
            default => $str,
        };
    }

    private function newInstance(UriInterface $uri): self
    {
        return match (true) {
            $uri->toString() === $this->uri->toString() => $this,
            default => new self($uri),
        };
    }

    public function withScheme(string $scheme): self
    {
        return $this->newInstance($this->uri->withScheme($this->filterInput($scheme)));
    }

    public function withUserInfo(string $user, ?string $password = null): self
    {
        return $this->newInstance($this->uri->withUserInfo($this->filterInput($user), $password));
    }

    public function withHost(string $host): self
    {
        return $this->newInstance($this->uri->withHost($this->filterInput($host)));
    }

    public function withPort(int|null $port): self
    {
        return $this->newInstance($this->uri->withPort($port));
    }

    public function withPath(string $path): self
    {
        return $this->newInstance($this->uri->withPath($path));
    }

    public function withQuery(string $query): self
    {
        return $this->newInstance($this->uri->withQuery($this->filterInput($query)));
    }

    public function withFragment(string $fragment): self
    {
        return $this->newInstance($this->uri->withFragment($this->filterInput($fragment)));
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     * @see Http::new()
     *
     * Create a new instance from a string.
     */
    public static function createFromString(Stringable|string $uri = ''): self
    {
        return self::new($uri);
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     * @see Http::fromComponents()
     *
     * Create a new instance from a hash of parse_url parts.
     *
     * @param InputComponentMap $components a hash representation of the URI similar
     *                                      to PHP parse_url function result
     */
    public static function createFromComponents(array $components): self
    {
        return self::fromComponents($components);
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     * @see Http::fromServer()
     *
     * Create a new instance from the environment.
     */
    public static function createFromServer(array $server): self
    {
        return self::fromServer($server);
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     * @see Http::new()
     *
     * Create a new instance from a URI object.
     */
    public static function createFromUri(Stringable|string $uri): self
    {
        return self::new($uri);
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     * @see Http::fromBaseUri()
     *
     * Create a new instance from a URI and a Base URI.
     *
     * The returned URI must be absolute.
     */
    public static function createFromBaseUri(Stringable|String $uri, Stringable|String|null $baseUri = null): self
    {
        return self::fromBaseUri($uri, $baseUri);
    }
}

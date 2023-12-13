<?php

namespace aranytoth\onesignal\resolver;

/**
 * Interface ResolverInterface
 * @package vasadibt\onesignal\resolver
 */
interface ResolverInterface
{
    /**
     * Resolve option array.
     *
     * @param array $data
     *
     * @return array
     */
    public function resolve(array $data);
}

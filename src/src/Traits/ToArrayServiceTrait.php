<?php
namespace App\Traits;

trait ToArrayServiceTrait
{
    /**
     * @param object $object
     * @return array
     */
    abstract public function toArray(object $object): array;

    /**
     * @param array $objects
     * @return array
     */
    public function collectionToArray(array $objects): array
    {
        $items = [];
        foreach ($objects as $object) {
            $items[] = $this->toArray($object);
        }

        return $items;
    }
}

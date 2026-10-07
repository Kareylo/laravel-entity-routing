<?php

namespace Kareylo\EntityRouting;

use Illuminate\Database\Eloquent\Model;

/**
 * Reads a dot notation path on an entity, like data_get(), but never
 * through an attribute an Eloquent model hides from serialization.
 */
class EntityAttributes
{
    public function get(mixed $entity, string $path): mixed
    {
        $target = $entity;

        foreach (explode('.', $path) as $segment) {
            if ($target instanceof Model && $this->isHidden($target, $segment)) {
                return null;
            }

            $target = data_get($target, $segment);
        }

        return $target;
    }

    private function isHidden(Model $model, string $attribute): bool
    {
        $visible = $model->getVisible();

        return in_array($attribute, $model->getHidden(), true)
            || ($visible !== [] && ! in_array($attribute, $visible, true));
    }
}

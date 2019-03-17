<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\MetaData\Validation;

use K3ssen\GeneratorBundle\MetaData\Property\BooleanMetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Property\DateMetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Property\DateTimeMetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Property\IntegerMetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Property\JsonMetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Property\ManyToManyMetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Property\MetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Property\OneToManyMetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Property\RelationMetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Property\SimpleArrayMetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Property\StringMetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Property\TextMetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Property\TimeMetaPropertyInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;

class MetaValidationFactory
{
    /** @var string */
    protected $metaValidationClass;

    public function setMetaValidationClass(string $class)
    {
        $this->metaValidationClass = $class;
    }

    public function createMetaValidation(MetaPropertyInterface $metaProperty, string $className, array $options = []): MetaValidationInterface
    {
        $className = $this->getConstraintFullClassName($className);
        return new $this->metaValidationClass($metaProperty, $className, $options);
    }

    protected function getConstraintFullClassName($className): string
    {
        if (strpos($className, '\\' ) === false) {
            return 'Symfony\\Component\\Validator\\Constraints\\'.$className;
        }
        return $className;
    }

    public function getConstraintOptions(MetaPropertyInterface $metaProperty = null)
    {
        $constraints = [];
        $constraintsDir = dirname ((new \ReflectionClass(Constraints\NotNull::class))->getFileName());
        foreach (scandir($constraintsDir) as $fileName) {
            $className = basename($fileName, '.php');
            $classFullName = $this->getConstraintFullClassName($className);
            if (class_exists($classFullName) && is_a($classFullName, Constraint::class, true)) {
                $constraints[$classFullName] = $className;
            }
        }

        foreach ($this->getBlackListConstraints($metaProperty) as $blacklistConstraint) {
            unset($constraints[$blacklistConstraint]);
        }

        return $constraints;
    }

    protected function getBlackListConstraints(MetaPropertyInterface $metaProperty = null)
    {
        $blackList = [
            Constraints\AbstractComparison::class,  //This isn't an actual constraint, since it's abstract
            //Constraints composed of other constraints are just too complex to be used in a generator like this.
            Constraints\Composite::class,
            Constraints\All::class,
            Constraints\Callback::class,
            Constraints\Existence::class,
            Constraints\Optional::class,
            Constraints\Collection::class,
            //What does traverse even do?
            Constraints\Traverse::class,
        ];
        if (!$metaProperty instanceof StringMetaPropertyInterface) {
            $blackList[] = Constraints\Bic::class;
            $blackList[] = Constraints\Currency::class;
            $blackList[] = Constraints\Iban::class;
            $blackList[] = Constraints\Image::class;
            $blackList[] = Constraints\Locale::class;
            $blackList[] = Constraints\Country::class;
            $blackList[] = Constraints\Ip::class;
            $blackList[] = Constraints\Uuid::class;
            //File and image actually aren't suitable for any orm-type, but one might use a string to setup a file/image property
            $blackList[] = Constraints\File::class;
            $blackList[] = Constraints\Image::class;
        }
        if (!$metaProperty instanceof StringMetaPropertyInterface && !$metaProperty instanceof TextMetaPropertyInterface) {
            $blackList[] = Constraints\NotBlank::class;
            $blackList[] = Constraints\Regex::class;
            $blackList[] = Constraints\Url::class;
            $blackList[] = Constraints\Email::class;
        }

        if (!$metaProperty instanceof StringMetaPropertyInterface && !$metaProperty instanceof IntegerMetaPropertyInterface) {
            //TODO: Not sure if these constraint would validate with only string or could work with integers as well
            $blackList[] = Constraints\CardScheme::class;
            $blackList[] = Constraints\Luhn::class;
            $blackList[] = Constraints\Isbn::class;
            $blackList[] = Constraints\Issn::class;
        }

        if (!$metaProperty instanceof IntegerMetaPropertyInterface
            && !$metaProperty instanceof DateTimeMetaPropertyInterface
            && !$metaProperty instanceof TimeMetaPropertyInterface
            && !$metaProperty instanceof DateMetaPropertyInterface
            //TODO: not sure if range would work with string if decimal is used.
        ) {
            $blackList[] = Constraints\Range::class;
        }

        if (!$metaProperty instanceof DateTimeMetaPropertyInterface) {
            $blackList[] = Constraints\DateTime::class;
        }
        if (!$metaProperty instanceof TimeMetaPropertyInterface) {
            $blackList[] = Constraints\Time::class;
        }
        if (!$metaProperty instanceof DateMetaPropertyInterface) {
            $blackList[] = Constraints\Date::class;
        }

        if (!$metaProperty instanceof RelationMetaPropertyInterface) {
            $blackList[] = Constraints\Valid::class;
        }
        if (!$metaProperty instanceof BooleanMetaPropertyInterface) {
            $blackList[] = Constraints\IsTrue::class;
            $blackList[] = Constraints\IsFalse::class;
        }

        if (!$metaProperty instanceof ManyToManyMetaPropertyInterface
            && !$metaProperty instanceof OneToManyMetaPropertyInterface
            && !$metaProperty instanceof SimpleArrayMetaPropertyInterface
            && !$metaProperty instanceof JsonMetaPropertyInterface //TODO: Not sure if json can be used as collection
        ) {
            $blackList[] = Constraints\Count::class;
        }

        return $blackList;
    }
}
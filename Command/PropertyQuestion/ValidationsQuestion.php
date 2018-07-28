<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command\PropertyQuestion;

use K3ssen\GeneratorBundle\Command\Helper\CommandInfo;
use K3ssen\GeneratorBundle\MetaData\Property\MetaPropertyInterface;
use K3ssen\GeneratorBundle\MetaData\Validation\MetaValidationFactory;
use K3ssen\GeneratorBundle\MetaData\Validation\MetaValidationInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\MissingOptionsException;

class ValidationsQuestion implements PropertyQuestionInterface
{
    protected const ACTION_VALIDATION_STOP = 0;
    protected const ACTION_VALIDATION_ADD = 1;
    protected const ACTION_VALIDATION_EDIT = 2;
    protected const ACTION_VALIDATION_REMOVE = 3;

    /** @var MetaValidationFactory */
    protected $metaValidationFactory;

    // TODO: use ask_validations configuration
    public function __construct(MetaValidationFactory $metaValidationFactory)
    {
        $this->metaValidationFactory = $metaValidationFactory;
    }

    public function doQuestion(CommandInfo $commandInfo, MetaPropertyInterface $metaProperty)
    {
        $commandInfo->saveTemporaryFile();
        $actionChoices = [
            static::ACTION_VALIDATION_STOP => null,
            static::ACTION_VALIDATION_ADD => 'Add new validation',
            static::ACTION_VALIDATION_EDIT => 'Edit validation',
            static::ACTION_VALIDATION_REMOVE => 'Remove validation',
        ];
        if (!$metaProperty->getValidations()->count()) {
            unset($actionChoices[static::ACTION_VALIDATION_EDIT], $actionChoices[static::ACTION_VALIDATION_REMOVE]);
        }
        $nextAction = $commandInfo->getIo()->choice('Validations (press <comment>[enter]</comment> to stop)' , $actionChoices);
        $nextAction = array_search($nextAction, $actionChoices);

        if ($nextAction === static::ACTION_VALIDATION_STOP) {
            return;
        }
        if ($nextAction === static::ACTION_VALIDATION_REMOVE) {
            $metaValidation = $this->askMetaPropertyValidationChoice($commandInfo, $metaProperty);
            $metaProperty->removeValidation($metaValidation);
            unset($metaValidation);
            $this->doQuestion($commandInfo, $metaProperty);
            return;
        }
        if ($nextAction === static::ACTION_VALIDATION_EDIT) {
            $metaValidation = $this->askMetaPropertyValidationChoice($commandInfo, $metaProperty);
            $validationOptions = get_class_vars($metaValidation->getClassName());
        } else {
            $validationClass = $this->askValidationChoice($commandInfo, $metaProperty);
            if (!$validationClass) {
                $this->doQuestion($commandInfo, $metaProperty);
            }
            $validationOptions = get_class_vars($validationClass);
            $requiredOptions = $this->getValidationRequiredOptions($validationClass);

            $customValidationOptions = [];
            foreach ($requiredOptions as $requiredOption) {
                if (!array_key_exists($requiredOption, $validationOptions)) {
                    throw new \RuntimeException(sprintf('Something unexpected went wrong: the required option %s does not exist in validationOptions: %s', $requiredOption, implode(',', array_keys($validationOptions))));
                }
                $customValidationOptions[$requiredOption] = $this->askValidationOptionValue($commandInfo, $requiredOption, $validationOptions[$requiredOption]);
            }
            $metaValidation = $this->metaValidationFactory->createMetaValidation($metaProperty, $validationClass, $customValidationOptions);
            $commandInfo->saveTemporaryFile();

            //Unset the options that aren't required, to prevent being bothered with unnecessary questions
            foreach ($validationOptions as $key => $validationOption) {
                if (!array_key_exists($key, $requiredOptions)) {
                    unset($validationOptions[$key]);
                }
            }
        }
        if (count($validationOptions)) {
            do {
                $editValidationOptionChoice = $commandInfo->getIo()->choice('Choose option to edit  (press <comment>[enter]</comment> to stop)', array_merge([null], array_keys($validationOptions)));
                if ($editValidationOptionChoice) {
                    $defaultValidationOptionValue = $metaValidation->getOptions()[$editValidationOptionChoice] ?? $validationOptions[$editValidationOptionChoice];
                    $customValidationOptions[$editValidationOptionChoice] = $this->askValidationOptionValue($commandInfo, $editValidationOptionChoice, $defaultValidationOptionValue);
                    $metaValidation->setOptions($customValidationOptions);
                    $commandInfo->saveTemporaryFile();
                }
            } while ($editValidationOptionChoice);
        }
        $this->doQuestion($commandInfo, $metaProperty);
    }

    protected function askMetaPropertyValidationChoice(CommandInfo $commandInfo, MetaPropertyInterface $metaProperty): MetaValidationInterface
    {
        $validations = $metaProperty->getValidations();
        $validationChoice = $commandInfo->getIo()->choice('Edit validation', $validations->toArray());
        foreach ($validations as $validation) {
            if ($validation->getClassName() === $validationChoice) {
                return $validation;
            }
        }
        throw new \RuntimeException(sprintf('No property found for choice %s', $validationChoice));
    }

    protected function askValidationOptionValue(CommandInfo $commandInfo, $validationOption, $defaultValue)
    {
        return $commandInfo->getIo()->ask($validationOption, $defaultValue, function ($value) {
            if (is_numeric($value)) {
                return (int) $value;
            }
            if (in_array($value, ['', '~', 'null', 'NULL'])) {
                return null;
            }
            if (in_array($value, ['true', 'TRUE'])) {
                return true;
            }
            if (in_array($value, ['false', 'FALSE'])) {
                return false;
            }
            return $value;
        });
    }

    protected function getValidationRequiredOptions(string $validationClass)
    {
        /** @var Constraint $validation */
        try {
            $validation = new $validationClass();
            $requiredOptions = $validation->getRequiredOptions();
        } catch (MissingOptionsException $exception) {
            $requiredOptions = $exception->getOptions();
        } catch(ConstraintDefinitionException $exception) {
            $requiredOptions = ['value'];
        }
        return $requiredOptions;
    }

    protected function askValidationChoice(CommandInfo $commandInfo, MetaPropertyInterface $metaProperty = null)
    {
        $options = $this->metaValidationFactory->getConstraintOptions($metaProperty);
        $commandInfo->getIo()->listing($options);
        $question = new Question('Add validation (optional)');
        $optionValues = array_values($options);
        $question->setAutocompleterValues(array_merge($optionValues, array_map('lcfirst', $optionValues), array_map('strtolower', $optionValues)));
        $question->setNormalizer(function ($choice) use ($options) {
            foreach ($options as $option) {
                if ($choice && strtolower($option) === strtolower($choice)) {
                    return $option;
                }
            }
            return $choice;
        });
        $validationChoice = $commandInfo->getIo()->askQuestion($question);
        return array_search($validationChoice, $options);
    }
}
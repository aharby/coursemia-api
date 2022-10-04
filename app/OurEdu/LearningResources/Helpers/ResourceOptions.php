<?php

namespace App\OurEdu\LearningResources\Helpers;


use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Repository\OptionRepository;

class ResourceOptions
{


    public function trueFalseDifficultyLevelOptions($key)
    {
        $optionRepo = new OptionRepository();
        $options = $optionRepo->pluckSlugByType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL);
        return formatFilter($options);
    }

    public function trueFalseLearningOutcomeOptions($key)
    {
        $optionRepo = new OptionRepository();
        $options = $optionRepo->pluckSlugByType(OptionsTypes::RESOURCE_LEARNING_OUTCOME);
        return formatFilter($options);
    }

    public function trueFalseTrueFalseTypeOptions($key)
    {
        $optionRepo = new OptionRepository();
        $options = $optionRepo->pluckSlugByType(OptionsTypes::TRUE_FALSE_TRUE_FALSE_TYPE);
        return formatFilter($options);
    }

    public function multipleChoiceDifficultyLevelOptions($key)
    {
        $optionRepo = new OptionRepository();
        $options = $optionRepo->pluckSlugByType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL);
        return formatFilter($options);
    }

    public function multipleChoiceLearningOutcomeOptions($key)
    {
        $optionRepo = new OptionRepository();
        $options = $optionRepo->pluckSlugByType(OptionsTypes::RESOURCE_LEARNING_OUTCOME);
        return formatFilter($options);
    }

    public function completeDifficultyLevelOptions($key)
    {
        $optionRepo = new OptionRepository();
        $options = $optionRepo->pluckSlugByType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL);
        return formatFilter($options);
    }

    public function completeLearningOutcomeOptions($key)
    {
        $optionRepo = new OptionRepository();
        $options = $optionRepo->pluckSlugByType(OptionsTypes::RESOURCE_LEARNING_OUTCOME);
        return formatFilter($options);
    }

    public function multipleChoiceMultipleChoiceTypeOptions($key)
    {
        $optionRepo = new OptionRepository();
        $options = $optionRepo->pluckSlugByType(OptionsTypes::MULTI_CHOICE_MULTIPLE_CHOICE_TYPE);
        return formatFilter($options);
    }


    public function videoVideoTypeOptions($key)
    {
        $optionRepo = new OptionRepository();
        $options = $optionRepo->pluckSlugByType(OptionsTypes::VIDEO_VIDEO_TYPE);
        return formatFilter($options);

    }

    public function audioAudioTypeOptions($key)
    {
        $optionRepo = new OptionRepository();
        $options = $optionRepo->pluckSlugByType(OptionsTypes::AUDIO_AUDIO_TYPE);
        return formatFilter($options);

    }

    public function dragDropDifficultyLevelOptions($key)
    {
        $optionRepo = new OptionRepository();
        $options = $optionRepo->pluckSlugByType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL);
        return formatFilter($options);

    }

    public function dragDropLearningOutcomeOptions($key)
    {
        $optionRepo = new OptionRepository();
        $options = $optionRepo->pluckSlugByType(OptionsTypes::RESOURCE_LEARNING_OUTCOME);
        return formatFilter($options);

    }

    public function dragDropDragDropTypeOptions($key)
    {
        $optionRepo = new OptionRepository();
        $options = $optionRepo->pluckSlugByType(OptionsTypes::DRAG_DROP_DRAG_DROP_TYPE);
        return formatFilter($options);

    }

    public function pdfPDFTypeOptions($key)
    {
        $optionRepo = new OptionRepository();
        $options = $optionRepo->pluckSlugByType(OptionsTypes::PDF_PDF_TYPE);
        return formatFilter($options);

    }

    public function picturePictureTypeOptions($key)
    {
        $optionRepo = new OptionRepository();
        $options = $optionRepo->pluckSlugByType(OptionsTypes::PICTURE_PICTURE_TYPE);
        return formatFilter($options);

    }

    public function matchingDifficultyLevelOptions($key)
    {
        $optionRepo = new OptionRepository();
        $options = $optionRepo->pluckSlugByType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL);
        return formatFilter($options);
    }

    public function matchingLearningOutcomeOptions($key)
    {
        $optionRepo = new OptionRepository();
        $options = $optionRepo->pluckSlugByType(OptionsTypes::RESOURCE_LEARNING_OUTCOME);
        return formatFilter($options);
    }

    public function multipleMatchingDifficultyLevelOptions($key)
    {
        $optionRepo = new OptionRepository();
        $options = $optionRepo->pluckSlugByType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL);
        return formatFilter($options);
    }

    public function multipleMatchingLearningOutcomeOptions($key)
    {
        $optionRepo = new OptionRepository();
        $options = $optionRepo->pluckSlugByType(OptionsTypes::RESOURCE_LEARNING_OUTCOME);
        return formatFilter($options);
    }

    public function hotspotDifficultyLevelOptions($key)
    {
        $optionRepo = new OptionRepository();
        $options = $optionRepo->pluckSlugByType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL);
        return formatFilter($options);
    }

    public function hotspotLearningOutcomeOptions($key)
    {
        $optionRepo = new OptionRepository();
        $options = $optionRepo->pluckSlugByType(OptionsTypes::RESOURCE_LEARNING_OUTCOME);
        return formatFilter($options);
    }
}

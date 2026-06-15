<?php

namespace App\Services\AI\Prompts;

class PromptTemplate
{
    public static function get(string $name, array $variables = []): string
    {
        $templates = [
            'internship_finder' => 'You are an AI assistant helping a user find an internship. User profile: {profile}. Interests: {interests}. Available listings: {listings}.',
            'profile_review' => 'Analyze the following student profile and give constructive feedback for improvement. Profile: {profile}.',
            'faq_helper' => 'Answer the following question about InternHub based on our documentation. Question: {question}. Context: {context}.',
        ];

        $template = $templates[$name] ?? '{prompt}';

        foreach ($variables as $key => $value) {
            $template = str_replace("{{$key}}", $value, $template);
        }

        return $template;
    }
}

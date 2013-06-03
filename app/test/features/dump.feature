Feature: debug
    As a prototype editor
    I benefit from Twig dump extension
    To inspect variables
    #http://twig.sensiolabs.org/doc/functions/dump.html

Scenario: dump query
    Given I am on "http://prontotype-foo.lo/dump?param1=value1"
    Then the "pre" element should contain "Symfony\Component\HttpFoundation\ParameterBag"
    And the "pre" element should contain "param1"
    And the "pre" element should contain "value1"

Scenario: debug mode disabled
    Given I am on "http://prontotype-bar.lo/dump?param1=value1"
    Then the response should not contain "Symfony\Component\HttpFoundation\ParameterBag"
    And the response should not contain "param1"
    And the response should not contain "value1"

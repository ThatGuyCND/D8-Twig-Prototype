Feature: extend-include
    As a prototype editor
    I benefit from Twig extend and include mechanisms
    To modularize my prototype

Scenario: extend prototype's layout
    Given I am on "/animals/reptiles"
    Then the ".test" element should contain "reptiles"

Scenario: extend prontotype system layout
    Given I am on "/animals/birds"
    Then the "#pt-content" element should contain "birds"

Scenario: include prototype's component
    Given I am on "/animals/reptiles"
    Then the "#pt-content" element should contain "footer content"
    And the ".footer" element should contain "footer content"


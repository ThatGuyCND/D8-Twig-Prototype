Feature: extend
    As a prototype editor
    I benefit from Twig extend mechanisms
    To modularize my prototype

Scenario: extend prototype's layout
    Given I am on "http://prontotype-foo.lo/extend"
    Then the "body" element should contain "pt-content"
    Then the "#pt-content" element should contain "extended block"

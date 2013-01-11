Feature: multiprototypes
    As a prototype editor
    I can easily run multiple prototype on a same prontotype instance
    In order to access useful functionalities

Scenario: check default prototype exists
    Given I am on "http://prontotype.lo"
    Then the "h1" element should contain "Prontotype is up and running."
    Given I am on "http://prontotype-test.lo"
    Then the "h1" element should contain "Test Prototype"

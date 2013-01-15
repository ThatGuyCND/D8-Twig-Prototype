Feature: navigation helper
    As a prototype editor
    I can generate a navigation for my prototype structure

Scenario Outline: default navigation
    Given I am on "/"
    Then the "#navigation-default" element should contain "<word>"

    Examples:
        | word |
        | overview |
        | animals |
        | mammals |
        | rats |
        | reptiles |
        | birds |

Scenario: navigation id
    Given I am on "/"
    Then the "#navigation-id" element should contain "navid"

Scenario: navigation without overview
    Given I am on "/"
    Then the "#navigation-hide-index" element should not contain "overview"

Scenario Outline: navigation of depth 1
    Given I am on "/"
    Then the "#navigation-depth-1" element should contain "<word>"

    Examples:
        | word |
        | animals |
        | home    |

Scenario Outline: navigation of depth 1
    Given I am on "/"
    Then the "#navigation-depth-1" element should not contain "<word>"

    Examples:
        | word |
        | overview |
        | mammals |
        | rats |
        | reptiles |
        | birds |


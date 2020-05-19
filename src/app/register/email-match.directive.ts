import { Directive, Input } from '@angular/core';
import { NG_VALIDATORS, Validator, ValidationErrors, FormGroup } from '@angular/forms';

import { EmailMatch } from './email-match.validator';

@Directive({
    selector: '[emailMatch]',
    providers: [{ provide: NG_VALIDATORS, useExisting: EmailMatchDirective, multi: true }]
})
export class EmailMatchDirective implements Validator {
    @Input('emailMatch') emailMatch: string[] = [];

  validate(formGroup: FormGroup): ValidationErrors {
    return EmailMatch(this.emailMatch[0], this.emailMatch[1])(formGroup);
  }
}

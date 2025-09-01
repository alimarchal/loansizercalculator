# Loan Program Structure Documentation

## Overview
The system now supports multiple loan programs within the same loan type, specifically for Fix and Flip loans which have two different appraisal programs.

## Loan Programs

### Fix and Flip
- **FULL APPRAISAL**: Traditional appraisal process
- **DESKTOP APPRAISAL**: Automated/desktop appraisal process

### Other Loan Types
- **New Construction**: Standard program (no sub-programs)
- **DSCR Rental**: Standard program (no sub-programs)

## Database Structure

### loan_types table
- `id`: Primary key
- `name`: Loan type name (e.g., "Fix and Flip")
- `loan_program`: Program variant (e.g., "FULL APPRAISAL", "DESKTOP APPRAISAL")

### experiences table
- Links to loan_types via `loan_type_id`
- Each experience range is created for each loan program variant

## Display Logic

### Matrix Headers
- Shows combined display name: "Fix and Flip - FULL APPRAISAL"
- Groups all rules under the appropriate program header

### Individual Rows
- Shows loan type name with program as subtitle
- Experience dropdowns show: "Experience Range (Loan Type - Program)"

## Creating New Loan Rules

### Experience Selection
- Dropdown shows all available experience ranges
- Format: "0 (Fix and Flip - FULL APPRAISAL)"
- Each program has its own set of experiences

### Validation
- System prevents duplicate combinations of:
  - Experience Level
  - FICO Band  
  - Transaction Type

## Usage Examples

### Full Appraisal Rules
- Experience: 0-10+ ranges for Fix and Flip - FULL APPRAISAL
- Independent configuration from Desktop Appraisal

### Desktop Appraisal Rules  
- Experience: 0-10+ ranges for Fix and Flip - DESKTOP APPRAISAL
- Can have different rates, limits, and requirements

## Benefits

1. **Clear Separation**: Each appraisal program has distinct rules
2. **Flexible Configuration**: Different rates and limits per program
3. **Easy Management**: Create/edit rules for specific programs
4. **Visual Clarity**: Clear headers distinguish between programs
5. **Scalable**: Easy to add new programs or loan types

## Future Extensions

The structure supports adding new loan programs by:
1. Adding new entries to loan_types table
2. Running ExperienceSeeder to create experience ranges
3. Creating loan rules through the interface

This ensures the system can grow with business needs while maintaining clear organization.

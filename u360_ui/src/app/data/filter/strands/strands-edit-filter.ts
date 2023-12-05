export class strandsEditFilter {
  private id: string;
  private name: string;
  private description: string;
  private age_group: string;

  constructor(
    id: string,
    name: string,
    description: string,
    age_group: string,
   ) {
      this.id = id;
      this.name = name;
      this.description = description;
      this.age_group = age_group;
    }
  }
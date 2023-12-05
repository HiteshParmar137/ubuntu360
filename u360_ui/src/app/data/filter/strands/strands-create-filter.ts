export class strandsCreateFilter {
    private name: string;
    private description: string;
    private age_group: string;
  
    constructor(
      name: string,
      description: string,
      age_group: string,
     ) {
        this.name = name;
        this.description = description;
        this.age_group = age_group;
    }
  }
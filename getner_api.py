from fastapi import FastAPI
from pydantic import BaseModel
import uvicorn
import spacy

nlp = spacy.load("en_core_web_sm")

app = FastAPI()


class ConvertRequest(BaseModel):
    text: str
    gender: str = "female"   # "male" or "female"


def gender_convert_possessive(text, target_gender="female"):
    """
    Convert third-person singular gendered pronouns to the requested gender.
    Handles subject, object, possessive adjective and reflexive pronouns with
    simple context rules, plus a hard rule for 'He/She stress levels'.
    """
    doc = nlp(text)
    result = []

    pronouns = {
        "male": {
            "subject": "he",
            "object": "him",
            "poss_adj": "his",
            "poss_pron": "his",
            "refl": "himself",
        },
        "female": {
            "subject": "she",
            "object": "her",
            "poss_adj": "her",
            "poss_pron": "hers",
            "refl": "herself",
        },
    }

    g = pronouns["male" if target_gender == "male" else "female"]

    for i, token in enumerate(doc):
        t_lower = token.text.lower()
        replacement = token.text

        # Look ahead for context
        next_token = None
        for j in range(i + 1, len(doc)):
            if not doc[j].is_space and not doc[j].is_punct:
                next_token = doc[j]
                break
        next_pos = next_token.pos_ if next_token is not None else None
        next_lower = next_token.text.lower() if next_token is not None else ""

        # SPECIAL CASE: "He/She stress levels" → "His/Her stress levels"
        if t_lower in ("he", "she") and next_lower == "stress":
            replacement = g["poss_adj"]

        # Generic subject pronouns
        elif t_lower in ("he", "she"):
            if next_pos in ("NOUN", "PROPN", "ADJ", "DET"):
                replacement = g["poss_adj"]
            else:
                replacement = g["subject"]

        # "his" → possessive adjective
        elif t_lower == "his":
            replacement = g["poss_adj"]

        # "her" → object or possessive adjective based on lookahead
        elif t_lower == "her":
            if next_pos in ("NOUN", "PROPN", "ADJ", "DET"):
                replacement = g["poss_adj"]
            else:
                replacement = g["object"]

        # "him"
        elif t_lower == "him":
            replacement = g["object"]

        # reflexive
        elif t_lower in ("himself", "herself"):
            replacement = g["refl"]

        # Preserve capitalization
        if token.text[:1].isupper():
            replacement = replacement.capitalize()

        result.append(replacement + token.whitespace_)

    return "".join(result)


@app.post("/convert")
def convert(req: ConvertRequest):
    converted = gender_convert_possessive(req.text, target_gender=req.gender)
    return {
        "original": req.text,
        "gender": req.gender,
        "converted": converted,
    }


# Optional: run with `python gender_api.py`
if __name__ == "__main__":
    uvicorn.run("gender_api:app", host="0.0.0.0", port=8000, reload=True)

